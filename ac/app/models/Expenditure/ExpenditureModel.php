<?php
    namespace App\models\Expenditure;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Auth;
    use DB;
class ExpenditureModel extends Model
{
  //By Niraj For getting start data entry count date 8-5-19
  function gettotaldataentryStart($const_type,$st_code='',$dist_no='')
  { 	DB::enableQueryLog();
  //echo $st_code.'dist_no==>'.$dist_no;
   if($const_type=="AC" && $dist_no!='0') {
      $gettotaldataentryStart = DB::table('expenditure_reports')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('candidate_nomination_detail.district_no',$dist_no)
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->groupBy('expenditure_reports.id')
      ->get();
      //->count();
      } elseif($const_type=="AC" && $dist_no=='0'){
        $gettotaldataentryStart =DB::table('expenditure_reports')
        ->where('ST_CODE',$st_code)
        ->count();
      }
       $gettotaldataentryStart=count($gettotaldataentryStart);
   // dd(DB::getQueryLog());
      return $gettotaldataentryStart;	
  }


  //By Niraj For getting finalize data entry count date 8-5-19
  function gettotaldataentryFinal($const_type,$st_code='',$dist_no='')
  { 	DB::enableQueryLog();
  if($const_type=="AC" && $dist_no!='0') {
    $gettotaldataentryFinal =DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
    ->where('candidate_nomination_detail.district_no',$dist_no)
    ->where('candidate_nomination_detail.application_status','=','6')
    ->where('candidate_nomination_detail.finalaccepted','=','1')
    ->where('candidate_nomination_detail.symbol_id','<>','200')
    ->where('expenditure_reports.ST_CODE',$st_code)
    ->where('finalized_status','1')
    ->groupBy('expenditure_reports.id')
      ->get();
      //->count();
    } elseif($const_type=="AC" && $dist_no=='0'){
      $gettotaldataentryFinal =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
      ->where('finalized_status','1')->count();
      }
       $gettotaldataentryFinal=count($gettotaldataentryFinal);
       //dd(DB::getQueryLog());
      return $gettotaldataentryFinal;	
  }


  //By Niraj For getting account loged count date 8-5-19
  function gettotallogedAccount($const_type,$st_code='',$dist_no='')
  { 	DB::enableQueryLog();
   if($const_type=="AC" && $dist_no!='0') {
      $gettotallogedAccount =DB::table('expenditure_reports')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('candidate_nomination_detail.district_no',$dist_no)
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->where('candidate_lodged_acct','Yes')
        ->groupBy('expenditure_reports.id')
      ->get();
      //->count();
      }elseif($const_type=="AC" && $dist_no=='0'){
        $gettotallogedAccount =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
        ->where('candidate_lodged_acct','Yes')->count();
      }  
      $gettotallogedAccount=count($gettotallogedAccount);
   //dd(DB::getQueryLog());
      return $gettotallogedAccount;	
  }


  
  //By Niraj For getting account loged count date 8-5-19
  function gettotalNotinTime($const_type,$st_code='',$dist_no='')
  { 	DB::enableQueryLog();
   if($const_type=="AC" && $dist_no!='0') {
      $gettotalNotinTime =DB::table('expenditure_reports')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('candidate_nomination_detail.district_no',$dist_no)
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->where('account_lodged_time','No')->count();
      }elseif($const_type=="AC" && $dist_no=='0'){
        $gettotalNotinTime =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
        ->where('account_lodged_time','No')->count();
      }  
   //dd(DB::getQueryLog());
      return $gettotalNotinTime;	
  }


  //By Niraj For Defects in format count date 8-5-19
  function gettotalDefectformats($const_type,$st_code='',$dist_no='')
  { 	//DB::enableQueryLog();
    if($const_type=="AC" && $dist_no!='0') {
      $gettotalDefectformats =DB::table('expenditure_reports')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('candidate_nomination_detail.district_no',$dist_no)
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->where('rp_act','No')->count();
      } elseif($const_type=="AC" && $dist_no=='0'){
        $gettotalDefectformats =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
        ->where('rp_act','No')->count();
      }  
   // dd(DB::getQueryLog());
      return $gettotalDefectformats;	
  }

  //By Niraj For Expense Understated count date 8-5-19
 function gettotalexpenseUnderStated($const_type,$st_code='',$dist_no='')
 { //	DB::enableQueryLog();
 if($const_type=="AC" && $dist_no!='0') {
    $gettotalexpenseUnderStated =DB::table('expenditure_understated')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
   
    
    ->where('expenditure_understated.district_no',$dist_no)
    ->where('candidate_nomination_detail.application_status','=','6')
    ->where('candidate_nomination_detail.finalaccepted','=','1')
    ->where('candidate_nomination_detail.symbol_id','<>','200')
    ->where('expenditure_understated.ST_CODE',$st_code)
    ->where('page_no_observation','Yes')
    ->groupBy('expenditure_understated.candidate_id')
    ->get();
    } elseif($const_type=="AC" && $dist_no=='0'){
     $gettotalexpenseUnderStated =DB::table('expenditure_understated')
     ->where('ST_CODE',$st_code)
    ->where('page_no_observation','Yes')->count();
   } 
   //dd($gettotalexpenseUnderStated);
  // dd(DB::getQueryLog());
     return $gettotalexpenseUnderStated;	
 }
 
 //By Niraj For party fund count date 8-5-19
 function gettotalPartyfund($const_type,$st_code='',$dist_no='')
 { 	DB::enableQueryLog();
// echo $const_type.'st_code=>'.$st_code.'const_no=>'.$const_no;
 if($const_type=="AC" && $dist_no!='0') {
    $gettotalPartyfund =DB::table('expenditure_fund_parties')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id') 
    ->select(DB::raw('IFNULL(SUM(expenditure_fund_parties.political_fund_cash + expenditure_fund_parties.political_fund_checque + expenditure_fund_parties.political_fund_kind),0) AS total_partyfund'))
    ->where('expenditure_fund_parties.ST_CODE',$st_code)
    ->where('candidate_nomination_detail.application_status','=','6')
    ->where('candidate_nomination_detail.finalaccepted','=','1')
    ->where('candidate_nomination_detail.symbol_id','<>','200')
    ->where('candidate_nomination_detail.district_no',$dist_no)
    ->first();
    }elseif($const_type=="AC" && $dist_no=='0'){
     $gettotalPartyfund =DB::table('expenditure_fund_parties')
    ->select(DB::raw('IFNULL(SUM(political_fund_cash + political_fund_checque + political_fund_kind),0) AS total_partyfund'))
    ->where('ST_CODE',$st_code)->first();
   }
  // dd(DB::getQueryLog());
     return $gettotalPartyfund;	
 }

   //By Niraj For party fund count date 8-5-19
  function gettotalOtherSourcesfund($const_type,$st_code='',$dist_no='')
  { 	DB::enableQueryLog();
   if($const_type=="AC" && $dist_no!='0') {
    $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id') 
    ->select(DB::raw('IFNULL(SUM(expenditure_fund_source.other_source_amount),0) AS total_otherSourcesfund'))
    ->where('expenditure_fund_source.ST_CODE',$st_code)
    ->where('candidate_nomination_detail.district_no',$dist_no)
    ->where('candidate_nomination_detail.application_status','=','6')
    ->where('candidate_nomination_detail.finalaccepted','=','1')
    ->where('candidate_nomination_detail.symbol_id','<>','200')
    ->first();
    }elseif($const_type=="AC" && $dist_no=='0'){
      $gettotalOtherSourcesfund =DB::table('expenditure_fund_source')
      ->select(DB::raw('IFNULL(SUM(other_source_amount),0) AS total_otherSourcesfund'))
      ->where('ST_CODE',$st_code)->first();
    } 
   // dd(DB::getQueryLog());
      return $gettotalOtherSourcesfund;	
  }

//By Niraj For getting Notice data by ECI entry count date 10-07-2019
function gettotalnoticeatDEO($const_type,$st_code='',$dist_no='')
{ 	DB::enableQueryLog();
    if($const_type=="AC" && $dist_no!='0') {
      $gettotalnoticeatDEO =DB::table('expenditure_reports')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('candidate_nomination_detail.district_no',$dist_no)
      ->where('candidate_nomination_detail.st_code',$st_code)
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
      ->where('expenditure_reports.final_by_ro','0')
      ->where('expenditure_reports.final_by_ceo','0')
      ->where(function($q) {
       $q->where('expenditure_reports.final_action', 'Notice Issued')
         ->orWhere('expenditure_reports.final_action','Reply Issued')
         ->orWhere('expenditure_reports.final_action', 'Hearing Done');
       })
       ->count();
      } elseif($const_type=="AC" && $dist_no=='0'){
        $gettotalnoticeatDEO =DB::table('expenditure_reports')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
      ->where('expenditure_reports.final_by_ro','0')
      ->where('expenditure_reports.final_by_ceo','0')
      ->where(function($q) {
       $q->where('expenditure_reports.final_action', 'Notice Issued')
         ->orWhere('expenditure_reports.final_action','Reply Issued')
         ->orWhere('expenditure_reports.final_action', 'Hearing Done');
       })
       ->count();
      }
 // dd(DB::getQueryLog());
    return $gettotalnoticeatDEO;	
}
####################Start Status Dashboard Function by Niraj 16-05-19##########################

        //By Niraj For getting start data entry count date 16-5-19
  function gettotalpartiallypending($const_type,$st_code='',$dist_no='')
  { 	DB::enableQueryLog();
  //echo $st_code.'pc==>'.$const_no;
  if($const_type=="AC" && $dist_no!='0') {
    $gettotalpartiallypending =DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
    ->where('candidate_nomination_detail.district_no',$dist_no)
    ->where('candidate_nomination_detail.application_status','=','6')
    ->where('candidate_nomination_detail.finalaccepted','=','1')
    ->where('candidate_nomination_detail.symbol_id','<>','200')
    ->where('expenditure_reports.ST_CODE',$st_code)
    ->where('expenditure_reports.finalized_status','=','1') 
    ->where('expenditure_reports.final_by_ro','1')
    ->whereNotNull('expenditure_reports.date_of_sending_deo')
    ->where(function($query) {
      $query->whereNull('expenditure_reports.date_of_receipt');
       $query->orwhere('expenditure_reports.date_of_receipt', '=','');
        })
      ->count();
    } elseif($const_type=="AC" && $dist_no=='0'){
      $gettotalpartiallypending =DB::table('expenditure_reports')
      ->where('ST_CODE',$st_code)
      ->where('final_by_ro','1')
      ->whereNotNull('date_of_sending_deo')
      ->where(function($query) {
        $query->whereNull('date_of_receipt');
         $query->orwhere('date_of_receipt', '=','');
          })
         ->count();
      }
    // dd(DB::getQueryLog());
      return $gettotalpartiallypending;	
  }

 function getdefaulter($const_type,$st_code='',$dist_no='')
  {  	DB::enableQueryLog();
  //echo $st_code.'pc==>'.$const_no;
   if($const_type=="AC" && $dist_no!='0') {
      $getdefaulter =DB::table('expenditure_understated')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
      ->select(DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
      DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
      ->having('totalobseramnt','<=','totalcandamnt')
      ->where('expenditure_understated.ST_CODE',$st_code)
      ->where('candidate_nomination_detail.district_no',$dist_no)
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->groupBy('expenditure_understated.candidate_id')
      ->get();
      } elseif($const_type=="AC" && $dist_no=='0'){
        $getdefaulter =DB::table('expenditure_understated')
        ->select(DB::raw('IFNULL(SUM(amt_as_per_observation),0) AS totalobseramnt'),
        DB::raw('IFNULL(SUM(amt_understated_by_candidate),0) AS totalcandamnt'))
        ->having('totalobseramnt','<=','totalcandamnt')
        ->where('ST_CODE',$st_code)
        ->groupBy('candidate_id')
        ->get();
      }
    //dd(DB::getQueryLog());
      return $getdefaulter;	
 }
 
   //By Niraj For getting finalize data entry by ceo count date 21-5-19
  function gettotalfinalbyceo($const_type,$st_code='',$dist_no='')
  { 	DB::enableQueryLog();
   //echo $const_type.'st_code'.$st_code.'const_no'.$const_no;
   
   if($const_type=="AC" && $dist_no!='0') {
      $gettotalfinalbyceo =DB::table('expenditure_reports')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('candidate_nomination_detail.district_no',$dist_no)
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->where('expenditure_reports.ST_CODE',$st_code)
      ->where('expenditure_reports.final_by_ceo','1')
      ->whereNotNull('expenditure_reports.date_of_receipt')
      ->whereNull('expenditure_reports.date_of_receipt_eci')
	  ->count();
      } elseif($const_type=="AC" && $dist_no=='0'){
        $gettotalfinalbyceo =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
        ->where('final_by_ceo','1')
        ->whereNotNull('date_of_receipt')
        ->whereNull('date_of_receipt_eci')
		->count();
      }
       // dd(DB::getQueryLog());
      return $gettotalfinalbyceo;	
  }

   //By Niraj For getting finalize data entry by ECI date 21-5-19
  function gettotalfinalbyeci($const_type,$st_code='',$dist_no='')
  { 	DB::enableQueryLog();
 if($const_type=="AC" && $dist_no!='0') {
      $gettotalfinalbyeci =DB::table('expenditure_reports')
      ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
      ->where('candidate_nomination_detail.district_no',$dist_no)
      ->where('candidate_nomination_detail.application_status','=','6')
      ->where('candidate_nomination_detail.finalaccepted','=','1')
      ->where('candidate_nomination_detail.symbol_id','<>','200')
      ->where('expenditure_reports.ST_CODE',$st_code)
       ->where('expenditure_reports.final_by_eci','1')
       ->whereNotNull('expenditure_reports.date_of_receipt_eci')
       ->count();
      } elseif($const_type=="AC" && $dist_no=='0'){
        $gettotalfinalbyeci =DB::table('expenditure_reports')->where('ST_CODE',$st_code)
        ->where('final_by_eci','1')
        ->whereNotNull('date_of_receipt_eci')
        ->count();
      }
      return $gettotalfinalbyeci;	
  }
  
  function gettotalreturn($const_type,$st_code='',$cons_no='',$return='')
  { 	
       
       //DB::enableQueryLog();
        
        if($const_type=="AC" && $st_code =='0' && $cons_no =='0'){
			 
            	  $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                         
                            ->where('candidate_nomination_detail.application_status','=','6')
                            ->where('candidate_nomination_detail.finalaccepted','=','1')
                            ->where('candidate_nomination_detail.symbol_id','<>','200')                           
                            ->where('expenditure_reports.return_status', '=', $return) 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')			 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
		     }elseif($const_type=="AC" && $st_code !='0' && $cons_no =='0'){
			  
            	  $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                          //  ->where('expenditure_reports.constituency_no','=',$cons_no)
                            ->where('candidate_nomination_detail.application_status','=','6')
                            ->where('candidate_nomination_detail.finalaccepted','=','1')
                            ->where('candidate_nomination_detail.symbol_id','<>','200')                           
                            ->where('expenditure_reports.return_status', '=', $return) 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')			 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
		    } elseif($const_type=="AC" && $st_code !='0' && $cons_no !='0'){	  
            	          $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no','=',$cons_no)
                            ->where('candidate_nomination_detail.application_status','=','6')
                            ->where('candidate_nomination_detail.finalaccepted','=','1')
                            ->where('candidate_nomination_detail.symbol_id','<>','200')                           
                            ->where('expenditure_reports.return_status', '=', $return) 	
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')		 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                   
	    	  }
                  else{
                      
                        $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                         
                            ->where('candidate_nomination_detail.application_status','=','6')
                            ->where('candidate_nomination_detail.finalaccepted','=','1')
                            ->where('candidate_nomination_detail.symbol_id','<>','200')                           
                            ->where('expenditure_reports.return_status', '=', $return) 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')			 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                  }
       
     
      return $gettotalreturn;	
  } 
   function gettotalreturnByDistrict($const_type,$st_code='',$dist_no='',$return='')
  { 	
       
       //DB::enableQueryLog();
        
        if($const_type=="AC" && $st_code !='0' && $dist_no =='0'){
			  
            	  $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.district_no',$dist_no)
                            ->where('candidate_nomination_detail.application_status','=','6')
                            ->where('candidate_nomination_detail.finalaccepted','=','1')
                            ->where('candidate_nomination_detail.symbol_id','<>','200')                           
                          ->where('expenditure_reports.return_status', '=', $return) 	
                          ->where('expenditure_reports.finalized_status', '=', '1')
                          ->where('expenditure_reports.final_by_ro', '=', '1')		 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
		    } elseif($const_type=="AC" && $st_code !='0' && $dist_no !='0'){	  
            	          $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.district_no',$dist_no)
                            ->where('candidate_nomination_detail.application_status','=','6')
                            ->where('candidate_nomination_detail.finalaccepted','=','1')
                            ->where('candidate_nomination_detail.symbol_id','<>','200')                           
                            ->where('expenditure_reports.return_status', '=', $return)
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1') 			 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                   
	    	  }
                  else{
                      
                        $gettotalreturn = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.district_no',$dist_no)
                            ->where('candidate_nomination_detail.application_status','=','6')
                            ->where('candidate_nomination_detail.finalaccepted','=','1')
                            ->where('candidate_nomination_detail.symbol_id','<>','200')                           
                            ->where('expenditure_reports.return_status', '=', $return)
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1') 			 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                  }
       
     
      return $gettotalreturn;	
  }
  ####################End Status Dashboard Function by Niraj 16-05-19##########################

  public static function GetExpeditureData($roleId=null,$constituency=null,$stcode=null,$condtition=null)
    {
          $user = Auth::user();
         if($roleId=="18" || $roleId=="5"){
          return DB::select("SELECT  er.*,NAC.title as nature_of_default_ac,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,cpd.cand_name as contensting_candiate FROM expenditure_reports AS er  INNER JOIN m_state ST ON
                                ST.ST_CODE = er.ST_CODE JOIN m_pc as pc ON pc.PC_NO=er.constituency_no and (pc.ST_CODE=er.st_code) left join expenditure_nature_of_default_ac as NAC ON NAC.id=er.nature_of_default_ac JOIN candidate_personal_detail AS cpd ON cpd.candidate_id=er.candidate_id where er.constituency_no ='$constituency' and er.ST_CODE='$stcode' $condtition ORDER BY er.id desc");
          }
          elseif($roleId=="4")
          {

          
          return DB::select("SELECT  er.*,NAC.title as nature_of_default_ac,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,cpd.cand_name as contensting_candiate FROM expenditure_reports AS er  LEFT JOIN m_state ST ON
                                ST.ST_CODE = er.ST_CODE  JOIN m_pc as pc ON pc.PC_NO=er.constituency_no and (pc.ST_CODE=er.st_code)  LEFT join expenditure_nature_of_default_ac as NAC ON NAC.id=er.nature_of_default_ac JOIN candidate_personal_detail AS cpd ON cpd.candidate_id=er.candidate_id where er.ST_CODE='$stcode' $condtition ORDER BY er.id desc");
          }
          elseif($roleId=="28"){

          
              return DB::select("SELECT  er.*,NAC.title as nature_of_default_ac,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,cpd.cand_name as contensting_candiate FROM expenditure_reports AS er  INNER JOIN m_state ST ON
                                ST.ST_CODE = er.ST_CODE JOIN m_pc as pc ON pc.PC_NO=er.constituency_no and (pc.ST_CODE=er.st_code)   left join expenditure_nature_of_default_ac as NAC ON NAC.id=er.nature_of_default_ac JOIN candidate_personal_detail AS cpd ON cpd.candidate_id=er.candidate_id
                                ORDER BY er.id desc");
          }
          else
          {}
    }



    public function getunewserbyuserid($uid=NULL,$roleID=NULL)
        {  

       if($roleId=="18" || $roleId=="5"){
            $data = DB::table('officer_login')->where('id',$uid )->join("m_pc",function($join){
                    $join->on("m_pc.PC_NO","=","officer_login.pc_no")
                        ->on("m_pc.ST_CODE","=","officer_login.st_code");
                          })
                      ->first();
                    return $data;
          } 
        elseif ($roleID=="4") {
            $data = DB::table('officer_login')->where('id',$uid )->join('m_state','m_state.ST_CODE','officer_login.st_code')->first();
            return $data;
          }  
          elseif($roleID=="28")
          {
              $data = DB::table('officer_login')->where('id',$uid )->first();
              return $data;
          }
        
        }
        

 public function GetExpeditureSingleData($candidate_id=NULL,$CONST_NO)
          {
         
             return DB::select("SELECT
                                      er.*,
                                      ND.title AS default_nature_text,
                                      CS.title AS current_status_text,
                                      ST.ST_NAME AS state,
                                      ac.AC_NAME AS AC_NAME,
                                      ac.AC_NO AS AC_NO,
                                      cpd.cand_name AS contensting_candiate
                                    FROM
                                      expenditure_reports AS er
                                    INNER JOIN
                                      m_state ST ON ST.ST_CODE = er.ST_CODE
                                    JOIN
                                      m_ac AS ac ON ac.AC_NO = er.constituency_no
                                    JOIN
                                      candidate_personal_detail AS cpd ON cpd.candidate_id = er.candidate_id
                                    LEFT JOIN
                                      expenditure_nature_of_default_ac ND ON ND.id = er.nature_of_default_ac
                                    LEFT JOIN
                                    expenditure_mis_current_sataus CS ON CS.id = er.current_status
                                    WHERE
                                      er.candidate_id = '$candidate_id' and  er.constituency_no = '$CONST_NO'"); 
        

}

    public static function singledata($id) {
      $data=  DB::select("SELECT  er.*,ST.ST_NAME AS state,pc.PC_NAME as PC_NAME,cpd.cand_name as contensting_candiate,NAC.title as nature_of_default_ac FROM expenditure_reports AS er  INNER JOIN m_state ST ON
                                ST.ST_CODE = er.ST_CODE left join expenditure_nature_of_default_ac as NAC ON NAC.id=er.nature_of_default_ac JOIN m_pc as pc ON pc.PC_ID=er.constituency_no JOIN candidate_personal_detail AS cpd ON cpd.candidate_id=er.candidate_id where er.id='$id'"); 
      return $data;
}

//manoj start here 
    public function GetScrutinyReportData($candidate_id) {
        return DB::select("SELECT
                                        ER.*,
                                        CPD.*,
                                        EFP.*,
                                        EUS.*,
                                        ER.candidate_id AS candidate_id,
                                        NAC.title AS nature_of_default_ac,
                                        ST.ST_NAME AS state,
                                        AC.AC_NAME AS AC_NAME,
                                        CPD.cand_name AS contensting_candiate,
                                        PT.PARTYNAME
                                    FROM
                                        expenditure_reports AS ER
                                    JOIN candidate_personal_detail AS CPD
                                    ON
                                        CPD.candidate_id = ER.candidate_id
                                    LEFT JOIN expenditure_fund_parties AS EFP
                                    ON
                                        EFP.candidate_id = ER.candidate_id
                                    LEFT JOIN expenditure_understates AS EUS
                                    ON
                                        EUS.candidate_id = ER.candidate_id
                                    INNER JOIN m_state ST ON
                                        ST.ST_CODE = ER.ST_CODE
                                    JOIN m_ac AS AC
                                    ON
                                        AC.AC_NO = ER.constituency_no AND(AC.ST_CODE = ER.st_code)
                                    LEFT JOIN expenditure_nature_of_default_ac AS NAC
                                    ON
                                        NAC.id = ER.nature_of_default_ac
                                    INNER JOIN candidate_nomination_detail CN ON
                                        CN.candidate_id = CPD.candidate_id
                                    INNER JOIN m_party PT ON
                                        PT.CCODE = CN.party_id
                                    WHERE
                                        ER.candidate_id = '$candidate_id'
                                    GROUP BY
                                        ER.candidate_id");
    }

    public function GetScrutinyUnderExpData($candidate_id,$acno) {
        return DB::select("SELECT * FROM  expenditure_understates as eu
         join expenditure_understated_masters as eum ON eum.id=eu.understated_type_id 
         WHERE eu.candidate_id = '$candidate_id' and eu.constituency_no = '$acno'");
    }
   
   //function for getting total breaching count by niraj 30-12-2019

  function gettotalbreaching($const_type,$st_code='',$const_no='')
  {   DB::enableQueryLog();
    if($const_no=='0') $const_no='';if($st_code=='0') $st_code='';
   // echo $st_code.'cons_no'.$const_no; 
  if($const_type=="AC" && $st_code=='' && $const_no =='') {
      $query="SELECT COUNT(DISTINCT(candidate_id)) as breachcount  FROM expenditure_understated";
    }elseif($const_type=="AC" && $st_code!='' &&  $const_no==''){
     $query="SELECT COUNT(DISTINCT(candidate_id)) as breachcount FROM expenditure_understated where ST_CODE ='".$st_code."'";
    }elseif($const_type=="AC" && $st_code!='' && $const_no!=''){
      $query="SELECT COUNT(DISTINCT(candidate_id)) as breachcount FROM expenditure_understated where ST_CODE ='".$st_code."' AND constituency_no = '".$const_no."'";
    } 
    $gettotalbreaching= (DB::select($query));
   //dd(DB::getQueryLog());
      return $gettotalbreaching; 
  }
  
   //Updated by Niraj 24-12-2019
    public function GetScrutinyUnderExpByitemData($candidate_id,$acno) {
		DB::enableQueryLog();
$query="select distinct n1.candidate_id,n1.date_understated,n1.amt_as_per_observation,
n1.amt_as_per_candidate,n1.amt_understated_by_candidate ,n1.expenditure_type,n1.page_no_observation,n2.description
from expenditure_understated n1,expenditure_understated n2
where n1.candidate_id=n2.candidate_id
and n1.expenditure_type=n2.expenditure_type AND n1.candidate_id = '".$candidate_id."' AND n1.constituency_no = '".$acno."' group by n1.expenditure_type,n1.date_understated";
          //dd(DB::getQueryLog());
        return DB::select($query);
        //return DB::select("SELECT * FROM  expenditure_understated WHERE candidate_id = '$candidate_id'");
    }

    public function GetScrutinysourecefundByitemData($candidate_id,$acno) {
        return DB::select("SELECT * FROM  expenditure_fund_source WHERE candidate_id = '$candidate_id' and constituency_no = '$acno' ");
    }

// end manoj here
public function getunewserbyuserid_uid_ceo($uid=NULL)
           {  
              $data = DB::table('expenditure_reports')->where('expenditure_reports.candidate_id',$uid )->join('m_pc', function ($join) {
              $join->on("m_pc.PC_NO","=","expenditure_reports.constituency_no")
                ->on("m_pc.ST_CODE","=","expenditure_reports.ST_CODE");
              })->join('candidate_personal_detail',"candidate_personal_detail.candidate_id",'=',"expenditure_reports.candidate_id")->first();
                          return $data;
           }


           public function getcandidatetotalexpenditure($candidate_id=null)
    {
        if(!empty($candidate_id))
        {
            $other_source_fund = DB::select("select sum(other_source_amount) as source_fund from expenditure_fund_source where candidate_id = '$candidate_id' ");
            $party_fund = DB::select("select political_fund_cash,political_fund_checque,political_fund_kind from expenditure_fund_parties where candidate_id='$candidate_id' ");
            $political_fund_cash = !empty($party_fund[0]->political_fund_cash)?$party_fund[0]->political_fund_cash:0;
             $political_fund_checque = !empty($party_fund[0]->political_fund_checque)?$party_fund[0]->political_fund_checque:0;
              $political_fund_kind = !empty($party_fund[0]->political_fund_kind)?$party_fund[0]->political_fund_kind:0;

            $total_exp = $other_source_fund[0]->source_fund + $political_fund_cash + $political_fund_checque + $political_fund_kind;

            return $total_exp;
        }
        else
        {
          return 0;
        }
    }

public function getpartyExp($candidate_id=null)
    {
      //echo $candidate_id;die;
        $candidate_id = rtrim($candidate_id, ',');
        if(!empty($candidate_id))
        {


            $other_source_fund = DB::select("select sum(other_source_amount) as source_fund from expenditure_fund_source where candidate_id IN ($candidate_id) ");

            $party_fund = DB::select("select sum(political_fund_cash) as political_fund_cash,sum(political_fund_checque) as political_fund_checque,sum(political_fund_kind) as political_fund_kind from expenditure_fund_parties where candidate_id IN ($candidate_id) ");
              $political_fund_cash = !empty($party_fund[0]->political_fund_cash)?$party_fund[0]->political_fund_cash:0;
              $political_fund_checque = !empty($party_fund[0]->political_fund_checque)?$party_fund[0]->political_fund_checque:0;
              $political_fund_kind = !empty($party_fund[0]->political_fund_kind)?$party_fund[0]->political_fund_kind:0;

              $total_exp = $other_source_fund[0]->source_fund + $political_fund_cash + $political_fund_checque + $political_fund_kind;
           // print_r($total_exp);die;
            return $total_exp;
        }
        else
        {
          return 0;
        }
    }

    public function getpartytotalexpenditure($party_id=null,$state=null,$ac=null)
    {
      if(!empty($state) && empty($ac)){
      $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE  er.st_code='$state' and cnd.application_status = '6' and cnd.party_id = '$party_id'  and cnd.finalaccepted = '1' and cnd.party_id <> 1180 and cnd.symbol_id <> 743");
      //print_r($candidate_ids);die;
      }
      elseif(!empty($state) && !empty($ac))
      {
      $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE  er.st_code='$state' and er.constituency_no='$ac' and cnd.party_id='$party_id' and cnd.application_status = '6' and cnd.party_id = '$party_id' and cnd.finalaccepted = '1' and cnd.party_id <> 1180 and cnd.symbol_id <> 743");
      }
      else{
      $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE cnd.application_status = '6' and cnd.party_id = '$party_id' and cnd.party_id <> 1180 and cnd.finalaccepted = '1' and cnd.party_id <> 743 ");
      }
       

      if(!empty($candidate_ids[0]->cand_ids)){
          $expenseTotal = $this->getpartyExp($candidate_ids[0]->cand_ids);
          return $expenseTotal;
        }
        else
        {
          return 0;
        }

    }
	
	#######################Start Fund Report  By Niraj 20-08-2019##############################

public function getGrandTotalExp($candidate_id=null)
{
  //echo $candidate_id;die;
    $candidate_id = rtrim($candidate_id, ',');
    if(!empty($candidate_id))
    {
        $grand_total = DB::select("select sum(grand_total_election_exp_by_cadidate) as grand_total_exp from expenditure_reports where candidate_id IN ($candidate_id) ");
        $grand_total_exp = $grand_total[0]->grand_total_exp;
        return $grand_total_exp;
    }
    else
    {
      return 0;
    }
}

public function getOtherSourcesExp($candidate_id=null)
{
    $candidate_id = rtrim($candidate_id, ',');
    if(!empty($candidate_id))
    {
        $other_source_fund = DB::select("select sum(other_source_amount) as source_fund from expenditure_fund_source where candidate_id IN ($candidate_id) ");
        $total_others_exp = $other_source_fund[0]->source_fund;
        return $total_others_exp;
    }
    else
    {
      return 0;
    }
}

public function getPoliticalpartyExp($candidate_id=null)
{
  //echo $candidate_id;die;
    $candidate_id = rtrim($candidate_id, ',');
    if(!empty($candidate_id))
    {
        $party_fund = DB::select("select sum(political_fund_cash) as political_fund_cash,sum(political_fund_checque) as political_fund_checque,sum(political_fund_kind) as political_fund_kind from expenditure_fund_parties where candidate_id IN ($candidate_id) ");
        $political_fund_cash = !empty($party_fund[0]->political_fund_cash)?$party_fund[0]->political_fund_cash:0;
        $political_fund_checque = !empty($party_fund[0]->political_fund_checque)?$party_fund[0]->political_fund_checque:0;
        $political_fund_kind = !empty($party_fund[0]->political_fund_kind)?$party_fund[0]->political_fund_kind:0;
        $total_political_party_exp = $political_fund_cash + $political_fund_checque + $political_fund_kind;
       // print_r($total_political_party_exp);die;
        return $total_political_party_exp;
    }
    else
    {
      return 0;
    }
}

 public function getcandidatesbyparties($party_id=null,$state=null,$ac=null)
 { 	DB::enableQueryLog();
   if(!empty($state) && empty($ac)){
   $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE  er.st_code='$state' and cnd.application_status = '6' and cnd.party_id = '$party_id'  and cnd.finalaccepted = '1' and cnd.party_id <> 1180 and cnd.symbol_id <> 743");
   //print_r($candidate_ids);die;
   }
   elseif(!empty($state) && !empty($ac))
   {
   $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE  er.st_code='$state' and er.constituency_no='$ac' and cnd.party_id='$party_id' and cnd.application_status = '6' and cnd.party_id = '$party_id' and cnd.finalaccepted = '1' and cnd.party_id <> 1180 and cnd.symbol_id <> 743");
   }
   else{
   $candidate_ids = DB::select("SELECT GROUP_CONCAT(DISTINCT(er.candidate_id)) as cand_ids FROM expenditure_reports as er join candidate_nomination_detail as cnd on cnd.candidate_id=er.candidate_id WHERE cnd.application_status = '6' and cnd.party_id = '$party_id' and cnd.party_id <> 1180 and cnd.finalaccepted = '1' and cnd.party_id <> 743 ");
   }
 // dd(DB::getQueryLog()) ;
   if(!empty($candidate_ids[0]->cand_ids)){ 
        //$countRec = count(explode(',',$candidate_ids[0]->cand_ids));
      // $expenseTotal = $this->getpartyExp($candidate_ids[0]->cand_ids);
       return $candidate_ids[0]->cand_ids;
     }
     else
     {
       return 0;
     }
 }

 ############################End Fund Report by Niraj ####################################################
public function getResultDeclarationDate(){
     $resultDeclarationDate = DB::table('m_schedule')->select(DB::raw("min(DATE_COUNT) as start_result_declared_date, max(DATE_COUNT) as last_result_declared_date"))->get()->toArray();      
       return !empty($resultDeclarationDate[0])?[ 'start_result_declared_date'=>$resultDeclarationDate[0]->start_result_declared_date,
                                                  'last_result_declared_date'=>$resultDeclarationDate[0]->last_result_declared_date
                                                ] :[];
 
}

 }