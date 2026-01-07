<?php

namespace App\adminmodel;

use Illuminate\Database\Eloquent\Model;
use DB;

class CandidateNApiModel extends Model
{
  function getnominationcnt($st_code,$const_no,$status='',$search_key,$electiontypeid,$scheduleid)
	{

	$rec =DB::table('candidate_nomination_detail')->where('party_id', '!=' ,'1180')->where('application_status','!=','11')
      ->Join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
      ->where('election_type_id',$electiontypeid);

      if(!empty($scheduleid)) {
        $rec->where('scheduleid',$scheduleid);
      }

      if(!empty($st_code) && !empty($const_no)){ 
        if($electiontypeid == '1' || $electiontypeid == '2') {
          $rec->where('st_code',$st_code)->where('pc_no',$const_no);
        }else{
          $rec->where('st_code',$st_code)->where('ac_no',$const_no);
        }
      }else{
		if(!empty($st_code)) {
        $rec->where('st_code',$st_code);
      }  
	  }

      if(!empty($status)) {
        $rec->where('application_status',$status);
      }

      if(!empty($search_key)){
        $rec->where('candidate_personal_detail.cand_name', 'like', "%{$search_key}%");
      }

      $reclist = $rec->get()->count();

      return $reclist;
  }
  
  function getnomination($st_code,$const_no,$status,$page,$search_key,$electiontypeid,$scheduleid)
	{
      $rec = DB::table('candidate_nomination_detail')
      ->Join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
      ->where('party_id', '!=' ,'1180')->where('application_status','!=','11')
      ->where('election_type_id',$electiontypeid);	
		
      if(!empty($scheduleid)) {
        $rec->where('scheduleid',$scheduleid);
      }

      if(!empty($st_code) && !empty($const_no)){ 
        if($electiontypeid == '1' || $electiontypeid == '2') {
          $rec->where('st_code',$st_code)->where('pc_no',$const_no);
        }else{
          $rec->where('st_code',$st_code)->where('ac_no',$const_no);
        }
      }else{
		if(!empty($st_code)) {
			$rec->where('st_code',$st_code);
      }  
	  }

      if(!empty($status)){
        $rec->where('application_status',$status);
      }

      if(!empty($search_key)){
        $rec->where('candidate_personal_detail.cand_name', 'like', "%{$search_key}%");
      }
	//  $reclist = $rec->get(); dd($reclist);

      $perPage = 25 ;
      $reclist = $rec->orderBy('candidate_nomination_detail.date_of_submit','DESC')->paginate($perPage,['*'],'page',$page);
      return $reclist;
    }

  function getnominationcntcontest($st_code,$const_no,$status='',$search_key,$electiontypeid,$scheduleid)
	{
	$rec =DB::table('candidate_nomination_detail')
		->Join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','!=','200')
        ->where('candidate_nomination_detail.finalize','=','1')
        ->Where('candidate_nomination_detail.party_id', '!=', '1180')
        ->where('election_type_id',$electiontypeid);

      if(!empty($scheduleid)) {
        $rec->where('scheduleid',$scheduleid);
      }

      if(!empty($st_code) && !empty($const_no)){ 
        if($electiontypeid == '1' || $electiontypeid == '2') {
          $rec->where('st_code',$st_code)->where('pc_no',$const_no);
        }else{
          $rec->where('st_code',$st_code)->where('ac_no',$const_no);
        }
      }else{
		if(!empty($st_code)) {
        $rec->where('st_code',$st_code);
      }  
	  }

      if(!empty($status)) {
        $rec->where('application_status',$status);
      }

      if(!empty($search_key)){
        $rec->where('candidate_personal_detail.cand_name', 'like', "%{$search_key}%");
      }

      $reclist = $rec->count();
      return $reclist;
  }

  function getnominationcontest($st_code,$const_no,$status,$page,$search_key,$electiontypeid,$scheduleid)
	{

    $rec =DB::table('candidate_nomination_detail')
    ->Join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
    ->where('candidate_nomination_detail.application_status','=','6')
    ->where('candidate_nomination_detail.finalaccepted','=','1')
    ->where('candidate_nomination_detail.symbol_id','!=','200')
    ->where('candidate_nomination_detail.finalize','=','1')
    ->Where('candidate_nomination_detail.party_id', '!=', '1180')
    ->where('election_type_id',$electiontypeid);

      if(!empty($scheduleid)) {
        $rec->where('scheduleid',$scheduleid);
      }

      if(!empty($st_code) && !empty($const_no)){ 
        if($electiontypeid == '1' || $electiontypeid == '2') {
          $rec->where('st_code',$st_code)->where('pc_no',$const_no);
        }else{
          $rec->where('st_code',$st_code)->where('ac_no',$const_no);
        }
      }else{
    if(!empty($st_code)) {
        $rec->where('st_code',$st_code);
      }  
    }

      if(!empty($status)) {
        $rec->where('application_status',$status);
      }

      if(!empty($search_key)){
        $rec->where('candidate_personal_detail.cand_name', 'like', "%{$search_key}%");
      }

      $perPage = 25 ;
      $reclist = $rec->orderBy('candidate_nomination_detail.new_srno','ASC')->paginate($perPage,['*'],'page',$page);
      return $reclist;
    }
	
	function getphasebystateac($st_code,$ac_code) {
      $pc = DB::table('m_ac')->where('ST_CODE',$st_code)->where('AC_NO',$ac_code)->first();
      return $pc;
  }

  function getnominationcontestpdf($st_code,$const_no,$status,$search_key,$electiontypeid,$scheduleid)
	{

    $rec =DB::table('candidate_nomination_detail')
    ->Join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
    ->where('candidate_nomination_detail.application_status','=','6')
    ->where('candidate_nomination_detail.finalaccepted','=','1')
    ->where('candidate_nomination_detail.symbol_id','!=','200')
    ->where('candidate_nomination_detail.finalize','=','1')
    ->Where('candidate_nomination_detail.party_id', '!=', '1180')
    ->where('election_type_id',$electiontypeid);

      if(!empty($scheduleid)) {
        $rec->where('scheduleid',$scheduleid);
      }

      if(!empty($st_code) && !empty($const_no)){ 
        if($electiontypeid == '1' || $electiontypeid == '2') {
          $rec->where('st_code',$st_code)->where('pc_no',$const_no);
        }else{
          $rec->where('st_code',$st_code)->where('ac_no',$const_no);
        }
      }else{
    if(!empty($st_code)) {
        $rec->where('st_code',$st_code);
      }  
    }

      if(!empty($status)) {
        $rec->where('application_status',$status);
      }

      if(!empty($search_key)){
        $rec->where('candidate_personal_detail.cand_name', 'like', "%{$search_key}%");
      }

      $reclist = $rec->orderBy('candidate_nomination_detail.new_srno','ASC')->get();

      return $reclist;
    }
}
