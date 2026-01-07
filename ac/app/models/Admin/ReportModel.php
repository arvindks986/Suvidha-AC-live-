<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class ReportModel extends Model
{
  

public function get_phases(){
    return DB::table('m_schedule')->get();
}

public function get_scrutny_report_ceo($data = array()){
  $sql = DB::table('m_election_details')->join('m_ac',[
          ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_ac.AC_NO']]);

    if(!empty($data['state_code'])){
       $sql->where('m_election_details.ST_CODE',$data['state_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('m_election_details.CONST_NO',$data['ac_no']);
    }

    if(!empty($data['const_type'])){
      $sql->where('m_election_details.CONST_TYPE',$data['const_type']);
    }

    if(!empty($data['phase_id'])){
      $sql->where('m_election_details.PHASE_NO',$data['phase_id']);
    }

    return $sql->orderBy('m_ac.AC_NO', 'ASC')->orderBy('m_ac.AC_NAME', 'ASC')
          ->select('m_election_details.*','m_ac.*','m_election_details.CONST_NO as CCODE','m_election_details.ST_CODE as st_code')->groupBy('m_election_details.CCODE')->get();
}

//not delete
function electiondetailsbystatecode($st_code, $consttype, $const = '')
{
  
    $rec =DB::table('m_election_details')
        ->join('m_ac',[
          ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_ac.AC_NO']
        ])
        ->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
        ->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
        ->select('m_election_details.*','m_ac.*')->get();
     
      return $rec;

    }		

  public function election_detail($data = array()){

    $sql = DB::table('m_election_details')->join('m_ac',[
          ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_ac.AC_NO']]);

    if(!empty($data['state_code'])){
       $sql->where('m_election_details.ST_CODE',$data['state_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('m_election_details.CONST_NO',$data['ac_no']);
    }

    if(!empty($data['const_type'])){
      $sql->where('m_election_details.CONST_TYPE',$data['const_type']);
    }

    if(!empty($data['phase_id'])){
      $sql->where('m_election_details.PHASE_NO',$data['phase_id']);
    }

    return $sql->orderBy('m_election_details.CONST_NO', 'ASC')
          ->select('m_election_details.*','m_ac.*','m_election_details.CONST_NO as CONST_NO')->first();
  
  }

  public function election_details($data = array()){
  
    $sql = DB::table('m_election_details')->join('m_ac',[
          ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_ac.AC_NO']]);

    if(!empty($data['state_code'])){
       $sql->where('m_election_details.ST_CODE',$data['state_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('m_election_details.CONST_NO',$data['ac_no']);
    }

    if(!empty($data['const_type'])){
      $sql->where('m_election_details.CONST_TYPE',$data['const_type']);
    }

    if(!empty($data['phase_id'])){
      $sql->where('m_election_details.PHASE_NO',$data['phase_id']);
    }
 

    return $sql->orderBy('m_ac.ST_CODE', 'ASC')->orderBy('m_ac.AC_NAME', 'ASC')
          ->select('m_election_details.*','m_ac.*')->get();
  
  }

  //not delete  
  function get_total_nomination($status, $data = array())
  {

    $sql = DB::table('candidate_nomination_detail as candidate');

    if(!empty($data['st_code'])){
      $sql->where('candidate.ST_CODE',$data['st_code']);
    }
    
    if($data['const_type']=='AC' && !empty($data['const_no'])){
      $sql->where('candidate.ac_no',$data['const_no']);
    }else{
      $sql->where('candidate.ac_no','!=','0')->where('candidate.ac_no','!=',NULL);
    }

    // if(!empty($data['final_accepted']) && !empty($data['symbol_excluded'])){
    //   $sql->where('candidate.finalaccepted','=','1')->where('candidate.symbol_id','!=','200');
    // }else if(!empty($data['final_accepted']) ){
    //   $status = 0;
    //   $sql->where('candidate.finalaccepted','=','1');
    // }


       if(!empty($data['final_accepted']) && !empty($data['symbol_excluded'])){
      $sql->where('candidate.finalaccepted','=','1')->where('candidate.symbol_id','!=','200')->where('candidate.finalize','=','1');
    }else if(!empty($data['final_accepted']) ){
      $status = 0;
      $sql->where('candidate.finalaccepted','=','1')->where('candidate.finalize','=','1');
    }





    if(!empty($data['phase'])){
      $sql->where('candidate.scheduleid',$data['phase']);
    }

    if($status != 1 && $status > 0){
      $status_array = [$status];
      $sql->whereIn('candidate.application_status',$status_array);
    }
 
    if(!empty($data['from_date']) && !empty($data['to_date'])){
      $sql->whereBetween('candidate.date_of_submit', [$data['from_date'], $data['to_date']]);
    }

    $query = $sql->where('candidate.party_id','!=','1180')->where('candidate.application_status','!=', '11')->count(); 

    return $query;

  }



// Without Nomination


  function get_total_without_nomination($status, $data = array())
  {




  // select candidate.* from (select a.nom_id,a.candidate_id from candidate_nomination_detail a )candidate
 //where candidate.nom_id not in (select b.nom_id from candidate_affidavit_detail b where candidate.nom_id=b.nom_id);

$sql = DB::table('candidate_nomination_detail as candidate')
                ->whereRaw('candidate.nom_id not in (select b.nom_id from candidate_affidavit_detail b where candidate.nom_id=b.nom_id)');
                






   // $sql = DB::table('candidate_nomination_detail as candidate');
   // $sql->where('candidate.nom_id','!=',$data['const_type']);

    if(!empty($data['st_code'])){
      $sql->where('candidate.ST_CODE',$data['st_code']);
    }
    
    if($data['const_type']=='AC' && !empty($data['const_no'])){
      $sql->where('candidate.ac_no',$data['const_no']);
    }else{
      $sql->where('candidate.ac_no','!=','0')->where('candidate.ac_no','!=',NULL);
    }

    // if(!empty($data['final_accepted']) && !empty($data['symbol_excluded'])){
    //   $sql->where('candidate.finalaccepted','=','1')->where('candidate.symbol_id','!=','200');
    // }else if(!empty($data['final_accepted']) ){
    //   $status = 0;
    //   $sql->where('candidate.finalaccepted','=','1');
    // }

    if(!empty($data['phase'])){
      $sql->where('candidate.scheduleid',$data['phase']);
    }

    // if($status != 1 && $status > 0){
    //   $status_array = [$status];
    //   $sql->whereIn('candidate.application_status',$status_array);
    // }
 
    if(!empty($data['from_date']) && !empty($data['to_date'])){
      $sql->whereBetween('candidate.date_of_submit', [$data['from_date'], $data['to_date']]);
    }

    $query = $sql->where('candidate.party_id','!=','1180')->where('candidate.application_status','!=', '11')->count(); 

//dd($query);
    return $query;

  }





       
  function get_nominations_without_affidavit($data = array())
  {
  


   $sql = DB::table('candidate_nomination_detail as candidate')
   ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate.candidate_id')
     ->join('m_party','m_party.CCODE','=','candidate.party_id')
     ->join('m_status','m_status.id','=','candidate.application_status')
      ->leftJoin('m_ac','m_ac.AC_NO','=','candidate.ac_no')
    ->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate.symbol_id')
                ->whereRaw(' candidate.nom_id not in (select b.nom_id from candidate_affidavit_detail b where candidate.nom_id=b.nom_id)');
                

 





    /*$sql = DB::table('candidate_affidavit_detail')
    ->join('candidate_nomination_detail','candidate_nomination_detail.nom_id','=','candidate_affidavit_detail.nom_id')
    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id')
    ->join('m_status','m_status.id','=','candidate_nomination_detail.application_status')
    ->join('m_party','m_party.CCODE','=','candidate_nomination_detail.party_id')
    ->leftJoin('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')
    ->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate_nomination_detail.symbol_id');
    */

    if(!empty($data['st_code'])){
      $sql->where('candidate.ST_CODE',$data['st_code']);
    }
    
    if($data['const_type']=='AC' && !empty($data['const_no'])){
      $sql->where('candidate.ac_no',$data['const_no']);
    }else{
      $sql->where('candidate.ac_no','!=','0')->where('candidate.ac_no','!=',NULL);
    }

    if(!empty($data['final_accepted']) && !empty($data['symbol_excluded'])){
      $sql->where('candidate.finalaccepted','=','1')->where('candidate.symbol_id','!=','200');
    }else if(!empty($data['final_accepted']) ){
      $status = 0;
      $sql->where('candidate.finalaccepted','=','1');
    }

    if(!empty($data['phase'])){
      $sql->where('candidate.scheduleid',$data['phase']);
    }

    // if($status != 1 && $status > 0){
    //   $status_array = [$status];
    //   $sql->whereIn('candidate_nomination_detail.application_status',$status_array);
    // }
 
    if(!empty($data['from_date']) && !empty($data['to_date'])){
      $sql->whereBetween('candidate.date_of_submit', [$data['from_date'], $data['to_date']]);
    }

    $query = $sql->where('party_id','!=',1180)->where('application_status','!=', 11)->select('candidate_personal_detail.*','m_status.status as status_name','candidate.nom_id as nomination_id','m_party.*','m_symbol.*','candidate.finalaccepted','candidate.application_status','candidate.new_srno','candidate.ST_CODE','m_ac.AC_NAME','candidate.ac_no as AC_NO')->groupBy('nom_id')->orderBy('candidate.new_srno','ASC');

    return $query->get();  

  }




















  function get_nominations($status, $data = array())
  {
  
    $sql = DB::table('candidate_nomination_detail')

    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id')
    ->join('m_status','m_status.id','=','candidate_nomination_detail.application_status')
    ->join('m_party','m_party.CCODE','=','candidate_nomination_detail.party_id')
    ->leftJoin('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')
    ->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate_nomination_detail.symbol_id');

    if(!empty($data['st_code'])){
      $sql->where('candidate_nomination_detail.ST_CODE',$data['st_code']);
    }
    
    if($data['const_type']=='AC' && !empty($data['const_no'])){
      $sql->where('candidate_nomination_detail.ac_no',$data['const_no']);
    }else{
      $sql->where('candidate_nomination_detail.ac_no','!=','0')->where('candidate_nomination_detail.ac_no','!=',NULL);
    }

    if(!empty($data['final_accepted']) && !empty($data['symbol_excluded'])){
      $sql->where('candidate_nomination_detail.finalaccepted','=','1')->where('candidate_nomination_detail.symbol_id','!=','200');
    }else if(!empty($data['final_accepted']) ){
      $status = 0;
      $sql->where('candidate_nomination_detail.finalaccepted','=','1');
    }

    if(!empty($data['phase'])){
      $sql->where('candidate_nomination_detail.scheduleid',$data['phase']);
    }

    if($status != 1 && $status > 0){
      $status_array = [$status];
      $sql->whereIn('candidate_nomination_detail.application_status',$status_array);
    }
 
    if(!empty($data['from_date']) && !empty($data['to_date'])){
      $sql->whereBetween('candidate_nomination_detail.date_of_submit', [$data['from_date'], $data['to_date']]);
    }

    $query = $sql->where('party_id','!=',1180)->where('application_status','!=', 11)->select('candidate_personal_detail.*','m_status.status as status_name','candidate_nomination_detail.nom_id as nomination_id','m_party.*','m_symbol.*','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.application_status','candidate_nomination_detail.new_srno','candidate_nomination_detail.ST_CODE','m_ac.AC_NAME','candidate_nomination_detail.ac_no as AC_NO')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get();  

  }


  public function get_ac_detail($filter_array = array()){
    $sql = DB::table('m_ac')->where('AC_NO',$filter_array['const_no'])->where('ST_CODE',$filter_array['st_code'])->first();
    if(!$sql){
      return '';
    }
    return $sql;
  }


// Last Movement report

     public  function count_nomination($nomination_type, $state)
  {
  
    $sql = DB::table('candidate_nomination_detail');
   // ->Join('candidate_affidavit_detail', 'candidate_nomination_detail.nom_id', '=', 'candidate_affidavit_detail.nom_id');
     // ->leftjoin('m_election_details','m_election_details.StatePHASE_NO','=','candidate_nomination_detail.state_phase_no')
      // ->leftjoin('m_schedule','m_schedule.SCHEDULENO','=','m_election_details.ScheduleID');

    
    if(!empty($state)){
      $sql->where('candidate_nomination_detail.ST_CODE',$state);
      // $sql->where('candidate_nomination_detail.application_status','<>',11);
      $sql->where('candidate_nomination_detail.nomination_type',$nomination_type);

    }
    
     
    
    $query = $sql->where('application_status','!=', 11)->where('party_id','!=',1180)->select('candidate_nomination_detail.nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

//$query = $sql->where('application_status','!=', 11)->where('party_id','!=',1180)->where('symbol_id','!=',200)->select('candidate_nomination_detail.nom_id')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get();  
   
/*
    if(!empty($data['final_accepted']) && !empty($data['symbol_excluded'])){
      $sql->where('candidate_nomination_detail.finalaccepted','=','1')->where('candidate_nomination_detail.symbol_id','!=','200');
    }else if(!empty($data['final_accepted']) ){
      $status = 0;
      $sql->where('candidate_nomination_detail.finalaccepted','=','1');
    }

    if(!empty($data['phase'])){
      $sql->where('candidate_nomination_detail.scheduleid',$data['phase']);
    }
    */

   

  }

    public function loginrecord($type, $data = array())
    {

      $sql = DB::table('officer_history');  
 //dd($data);
    
    if(!empty($data['st_code'])){
      
      $sql->where('officer_login_id', 'like', '%'.'ROAC'.$data['st_code'].'%');
      // $sql->where('candidate_nomination_detail.application_status','<>',11);
      //$sql->where('candidate_nomination_detail.nomination_type',$nomination_type);
    }
    
     $date='2023-10-20';
    
    $query = $sql->where('login_date','=', $data['date'])->select('officer_history.id')->groupBy('officer_history.officer_login_id')->orderBy('officer_history.id','ASC');

    return $query->get(); 



    }


    public function NominationRecv($nomination_type,$data=array())
    {

      $sql = DB::table('candidate_nomination_detail');  

    
    if(!empty($data['date'])){
      
      $sql->where('candidate_nomination_detail.created_by', 'like', '%'.'ROAC'.$data['st_code'].'%');
      $sql->where('candidate_nomination_detail.application_status','<>',11);
      $sql->where('candidate_nomination_detail.party_id','<>',1180);
       $sql->where('candidate_nomination_detail.symbol_id','<>',200);

     // $sql->where('candidate_nomination_detail.nomination_type',$nomination_type);
    }
    
     $date='2023-10-20';
    
    $query = $sql->where('candidate_nomination_detail.added_create_at','=', $data['date'])->select('candidate_nomination_detail.nom_id')->groupBy('candidate_nomination_detail.nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get(); 




    }




public function NominationDetails_tgcount($type)
    {

      $sql = DB::table('candidate_nomination_detail')

    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id')
    ->join('m_status','m_status.id','=','candidate_nomination_detail.application_status')
    ->join('m_party','m_party.CCODE','=','candidate_nomination_detail.party_id')
    ->leftJoin('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')
    ->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate_nomination_detail.symbol_id');

    // if(!empty($data['st_code'])){
    //   $sql->where('candidate_nomination_detail.ST_CODE',$data['st_code']);
    // }

    if(!empty($type)){
      $sql->where('candidate_personal_detail.cand_gender','=',$type);
    }
  
    

    $query = $sql->where('party_id','!=',1180)
    ->where('candidate_nomination_detail.application_status','=', 6)
    ->where('candidate_nomination_detail.finalaccepted','=', '1')
    ->where("candidate_nomination_detail.symbol_id", "!=",  '200')
     ->where("candidate_nomination_detail.finalize", "=",  '1')
    // ->select('candidate_personal_detail.*','m_status.status as status_name','candidate_nomination_detail.nom_id as nomination_id','m_party.*','m_symbol.*','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.application_status','candidate_nomination_detail.new_srno','candidate_nomination_detail.ST_CODE','m_ac.AC_NAME','candidate_nomination_detail.ac_no as AC_NO')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    ->select('candidate_nomination_detail.nom_id')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get();  
           
          

    }


    public function NominationDetails($cate,$type,$state,$phaseid)
    {

      if(!empty($phaseid)) {
   
 
                  $p1=1;$p2=10;
                  if($phaseid==1){
                      $phaseid=[1,10];
                  }else{
                    $phaseid=array($phaseid);

                  }
       }

      $sql = DB::table('candidate_nomination_detail')

    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id')
    ->join('m_status','m_status.id','=','candidate_nomination_detail.application_status')
    ->join('m_party','m_party.CCODE','=','candidate_nomination_detail.party_id')
    ->leftJoin('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')
   // ->Join('candidate_affidavit_detail', 'candidate_nomination_detail.nom_id', '=', 'candidate_affidavit_detail.nom_id')
    ->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate_nomination_detail.symbol_id');

    if(!empty($state)){
      $sql->where('candidate_nomination_detail.ST_CODE',$state);
    }

    if(!empty($type)){
      $sql->where('candidate_personal_detail.cand_gender','=',$type);
    }
  
    if(!empty($cate)){
      $sql->whereIn('candidate_personal_detail.cand_category',['st','sc']);
    }
     
      if (!empty($phaseid)) {
         $sql->whereIN('candidate_nomination_detail.scheduleid',$phaseid);
       // $sql->whereIN('candidate_nomination_detail.state_phase_no',$phasearray);
               
              // $sql->where('candidate_nomination_detail.state_phase_no','=',$phasearray);
            }
    $query = $sql->where('party_id','!=',1180)
    ->where('candidate_nomination_detail.application_status','=', 6)
    ->where('candidate_nomination_detail.finalaccepted','=', '1')
    ->where("candidate_nomination_detail.symbol_id", "!=",  '200')
    ->where("candidate_nomination_detail.finalize", "=",  '1')
    // ->select('candidate_personal_detail.*','m_status.status as status_name','candidate_nomination_detail.nom_id as nomination_id','m_party.*','m_symbol.*','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.application_status','candidate_nomination_detail.new_srno','candidate_nomination_detail.ST_CODE','m_ac.AC_NAME','candidate_nomination_detail.ac_no as AC_NO')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    ->select('candidate_nomination_detail.nom_id')->groupBy('candidate_nomination_detail.nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get();  
           
          

    }

      public function Contesting_Cand($type, $state,$phaseid)
      {

           $sql = DB::table('candidate_nomination_detail')

    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id')
    ->join('m_status','m_status.id','=','candidate_nomination_detail.application_status')
    ->join('m_party','m_party.CCODE','=','candidate_nomination_detail.party_id')
    ->leftJoin('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')
    ->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate_nomination_detail.symbol_id');

    if(!empty($state)){
      $sql->where('candidate_nomination_detail.ST_CODE',$state);
    }

    if(!empty($type)){
      $sql->where('candidate_personal_detail.is_criminal','=',$type);
    }
    if (!empty($phaseid)) {
               
      $sql->where('candidate_nomination_detail.state_phase_no','=',$phaseid);
    }
  

    $query = $sql->where('candidate_nomination_detail.party_id','!=',1180)
    ->where('candidate_nomination_detail.application_status','=', 6)
    ->where('candidate_nomination_detail.finalaccepted','=', '1')
    ->where("candidate_nomination_detail.symbol_id", "!=",  '200')
    ->where("candidate_nomination_detail.finalize", "=",  '1')
    // ->select('candidate_personal_detail.*','m_status.status as status_name','candidate_nomination_detail.nom_id as nomination_id','m_party.*','m_symbol.*','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.application_status','candidate_nomination_detail.new_srno','candidate_nomination_detail.ST_CODE','m_ac.AC_NAME','candidate_nomination_detail.ac_no as AC_NO')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    ->select('candidate_nomination_detail.nom_id')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get();  



      }

      public function AfterScrutiny($type,$state,$phase)
      {


        $sql = DB::table('candidate_nomination_detail')

    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id');
   // ->Join('candidate_affidavit_detail', 'candidate_nomination_detail.nom_id', '=', 'candidate_affidavit_detail.nom_id');
   

    if(!empty($state)){
      $sql->where('candidate_nomination_detail.ST_CODE',$state);
    }
      if(!empty($phase)){
      $sql->where('candidate_nomination_detail.state_phase_no','=',$phase);
    }

    if(!empty($type) && $type==2){
      $sql->where('candidate_nomination_detail.nomination_type','=',0);
    }
  
     if(!empty($type) && $type==1){
       $sql->where('candidate_nomination_detail.nomination_type','=',1);
    }

    $query = $sql->where('party_id','!=',1180)
    //->where('candidate_nomination_detail.application_status','=', 6)
    ->whereIn('candidate_nomination_detail.application_status', ['4','6'])
   // ->where('candidate_nomination_detail.finalaccepted','=', '1')
   // ->where("candidate_nomination_detail.symbol_id", "!=",  '200')
    // ->select('candidate_personal_detail.*','m_status.status as status_name','candidate_nomination_detail.nom_id as nomination_id','m_party.*','m_symbol.*','candidate_nomination_detail.finalaccepted','candidate_nomination_detail.application_status','candidate_nomination_detail.new_srno','candidate_nomination_detail.ST_CODE','m_ac.AC_NAME','candidate_nomination_detail.ac_no as AC_NO')->groupBy('nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    ->select('candidate_nomination_detail.nom_id');

    return $query->get();  




      }

      public static function get_count_payment_wise($state,$mode) {

       // dd($filter);


        $sql = DB::table('candidate_nomination_detail')

    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id')
    //->Join('candidate_affidavit_detail', 'candidate_nomination_detail.nom_id', '=', 'candidate_affidavit_detail.nom_id')
   
    ->join('nomination_application','nomination_application.candidate_id','=','candidate_nomination_detail.old_candidate_id');
    // ->join('payment_details_common','payment_details_common.candidate_id','=','nomination_application.candidate_id')
   // ->join('challan_payment','challan_payment.candidate_id','=','nomination_application.candidate_id')
    //->leftJoin('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')
    //->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate_nomination_detail.symbol_id');



    

    if($mode=='online'){
      $sql->join('payment_details_common', [
        ['nomination_application.st_code', '=', 'payment_details_common.st_code'], 
        ['nomination_application.candidate_id', '=', 'payment_details_common.candidate_id']
      ])->where('payment_details_common.bank_transaction_status', '=', '1');
    }

    if($mode==''){
      $finalize = ($filter['finalize']=='1') ? '1' : '0' ;
      $sql->where('nomination_application.finalize_after_payment', '=', $finalize);
    }

    if($mode=='challan'){
      $sql->select('challan_payment.challan_receipt')->join('challan_payment', [
        ['nomination_application.st_code', '=', 'challan_payment.st_code'], 
        ['nomination_application.candidate_id', '=', 'challan_payment.candidate_id']
      ])->where('challan_receipt', '<>', '');
    }

    if($mode=='cash'){
      $sql->select('challan_payment.payByCash', 'challan_payment.pay_by_cash_paid', 'challan_payment.date_time_of_pbc')->join('challan_payment', [
        ['nomination_application.st_code', '=', 'challan_payment.st_code'], 
        ['nomination_application.candidate_id', '=', 'challan_payment.candidate_id']
      ])
      ->where('payByCash', '=', '1')
      ->where('pay_by_cash_paid', '=', '1')
      ->where('date_time_of_pbc', '!=', null);
    }

    // if(!empty($filter['st_code'])){
    //   ->where('nomination_application.st_code', $filter['st_code']);
    //    ->where('party_id','!=',1180);
    //    ->where('nomination_application.pplication_type','=',2)
    //    ->where('nomination_application.finalize', '=', 1);
    // }

     $query = $sql->where('candidate_nomination_detail.party_id','!=',1180)->where('nomination_application.application_type','=',2)->where('nomination_application.finalize', '=', 1) ->where('nomination_application.st_code', $state)
     ->whereIn('candidate_nomination_detail.application_status', ['4','6'])
   // ->where('candidate_nomination_detail.application_status','=', 6)

     ->select('candidate_nomination_detail.nom_id');

    return $query->get();  
   

   // $final_data = $fetch_data->groupBy('nomination_application.candidate_id')->get()->count(); //dd($final_data);
   // return $final_data;
  }

    

public static function getphase() {

   $get=DB::table('m_election_details')->groupBy('m_election_details.StatePHASE_NO')->get();

   return $get;




}

  public function electionDate($state){

       $sql = DB::table('m_schedule')

    ->join('m_election_details','m_schedule.SCHEDULEID','=','m_election_details.ScheduleID');
     
   
      $query = $sql->where('m_election_details.ST_CODE','=',$state)
      ->select('m_schedule.LDT_IS_NOM')->groupBy('m_election_details.ST_CODE');
    return $query->get();  


  }


   public function Contesting_Cand_age($age,$state,$phaseid)
    {

  if(!empty($phaseid)) {
  $contestingNominationcandfilter = "SELECT DISTINCT StatePHASE_NO FROM m_election_details where  PHASE_NO IN($phaseid)";

    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
    $valueis=array();
    foreach ($EciPhaseInfoData as $key => $value) {
      
       $valueis[]=$value->StatePHASE_NO;
    }
    
    $imp=  implode(',', $valueis); 
      $phasearray=array($imp);
 }

      $sql = DB::table('candidate_nomination_detail')

    ->join('candidate_personal_detail','candidate_personal_detail.candidate_id','=','candidate_nomination_detail.candidate_id')
    ->join('m_status','m_status.id','=','candidate_nomination_detail.application_status')
    ->join('m_party','m_party.CCODE','=','candidate_nomination_detail.party_id')
    ->leftJoin('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')
   // ->Join('candidate_affidavit_detail', 'candidate_nomination_detail.nom_id', '=', 'candidate_affidavit_detail.nom_id')
    ->leftJoin('m_symbol','m_symbol.SYMBOL_NO','=','candidate_nomination_detail.symbol_id');

    if(!empty($state)){
      $sql->where('candidate_nomination_detail.ST_CODE',$state);
    }

    if(!empty($age) && ($age==25)){
     
      $sql->whereBetween('candidate_personal_detail.cand_age',[18, 40]);
    }
    if(!empty($age) && ($age==40)){
     
      $sql->whereBetween('candidate_personal_detail.cand_age',[41, 60]);
    }
    if(!empty($age) && ($age==60)){
     
      $sql->whereBetween('candidate_personal_detail.cand_age',[61, 130]);
    }
  
   if(!empty($phaseid)){
       $sql->whereIN('candidate_nomination_detail.state_phase_no',$phasearray);
      //$sql->where('candidate_nomination_detail.state_phase_no','=',$phaseid);
    }
     
      
    $query = $sql->where('party_id','!=',1180)
    ->where('candidate_nomination_detail.application_status','=', 6)
    ->where('candidate_nomination_detail.finalaccepted','=', '1')
    ->where("candidate_nomination_detail.symbol_id", "!=",  '200')
    ->where("candidate_nomination_detail.finalize", "=",  '1')
    

    ->select('candidate_nomination_detail.nom_id')->groupBy('candidate_nomination_detail.nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get();  
           
          

    }






 public function count_ac($st_code)
    {


       $sql = DB::table('m_election_details');

    //->join('m_election_details','m_schedule.SCHEDULEID','=','m_election_details.ScheduleID');
     
   
      $query = $sql->where('m_election_details.ST_CODE','=',$st_code)
      ->select('m_election_details.const_no')->groupBy('m_election_details.const_no');
      $count=count($query->get());
    return $count;  

    }
    public function count_ac_wise($st_code,$phase)
    {




    $contestingNominationcandfilter = "SELECT s.`ST_NAME`,s.`ST_CODE`,e.StatePHASE_NO FROM m_election_details e  JOIN `m_state` s ON e.ST_CODE=s.ST_CODE AND e.`PHASE_NO`IN ($phase) AND e.`ST_CODE`='".$st_code."' ";
    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
    $aa=count($EciPhaseInfoData);
    return $aa;

    }


public  function count_nomination_phase($nomination_type, $state, $phaseid)
  {

    // $contestingNominationcandfilter = "SELECT DISTINCT StatePHASE_NO FROM m_election_details where  PHASE_NO IN($phase)";

    // $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
    // $valueis=array();
    // foreach ($EciPhaseInfoData as $key => $value) {
      
    //    $valueis[]=$value->StatePHASE_NO;
    // }
    
    // $imp=  implode(',', $valueis); 
    //   $phasearray=array($imp);

       $p1=1;$p2=10;
                  if($phaseid==1){
                      $phaseid=[1,10];
                  }else{
                    $phaseid=array($phaseid);

                  }
    $sql = DB::table('candidate_nomination_detail');

    
    if(!empty($state)){
      $sql->where('candidate_nomination_detail.ST_CODE',$state);
        // $sql->whereIN('candidate_nomination_detail.state_phase_no',$phasearray);
         $sql->whereIN('candidate_nomination_detail.scheduleid',$phaseid);
      $sql->where('candidate_nomination_detail.nomination_type',$nomination_type);

    }
    
     
    
    $query = $sql->where('application_status','!=', 11)->where('party_id','!=',1180)->select('candidate_nomination_detail.nom_id')->orderBy('candidate_nomination_detail.new_srno','ASC');

    return $query->get();  
   
  }

  public function electionDate_phase($state,$phases){

        

    $contestingNominationcandfilter = "SELECT e.LDT_IS_NOM FROM m_schedule e  JOIN `m_election_details` s ON e.SCHEDULEID=s.ScheduleID AND s.`PHASE_NO`IN ($phases) AND s.`ST_CODE`IN ('".$state."') group By s.ST_CODE ";
    $EciPhaseInfoData = DB::select($contestingNominationcandfilter);
    $aa=$EciPhaseInfoData;
    return $aa;


  }






//  End Last Movement report




























  

}