<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class ECIModel extends Model
{
   
     function Allelection()
            {  
             
              $list = DB::table('m_election_details')->orderBy('m_election_details.ST_CODE', 'ASC')->orderBy('m_election_details.CONST_NO', 'ASC')->get();
              return $list;
            }
    function getcurrentelection($st_code)
            {
              $rec =DB::table('m_cur_elec')->where('ST_CODE', $st_code)->first();
              return $rec;
             }
    function checkcurrentelection($st_code,$month,$year,$deltype,$eletype,$contype,$scheduleid)
            {
             $rec =DB::table('m_cur_elec')->where('ST_CODE', $st_code)->where('MONTH', $month)->where('YEAR', $year)->where('DelmType', $deltype)
                          ->where('ElecType', $eletype)->where('ConstType', $contype)->where('ScheduleID', $scheduleid)->first();
            // $rec =DB::table('m_cur_elec')->where('ST_CODE', $st_code)->where('ElecType', $eletype)->where('ConstType', $contype)->first();
                       
              if(isset($rec))
                return 1;
              else
                 return 0;
            }
     function getallcuttentelection()
            {
              $rec =DB::table('m_cur_elec')->where('CURRENTELECTION','Y')->get();
              return $rec;
            }
      function getallelectionphasewise()
            {
             $list = DB::table('m_election_details')
                      ->select(DB::raw('count(StatePHASE_NO) as total'),'StatePHASE_NO','ST_CODE','ScheduleID','CONST_TYPE','ELECTION_TYPE') 
                      ->groupBy('m_election_details.StatePHASE_NO','ST_CODE','ScheduleID','CONST_TYPE','ELECTION_TYPE')
                       ->orderBy('ST_CODE','ASC') ->orderBy('StatePHASE_NO','ASC')
                      ->get();
             /*        
             $results = DB::select( DB::raw("SELECT count('StatePHASE_NO'), m_election_details.* FROM m_election_details WHERE CURRENTELECTION='Y' GROUP BY StatePHASE_NO") );
              */
              return $list;
            }
     function getelection_details($st_code,$sched_id,$phaseno,$cons_type)
            {
              $rec =DB::table('m_election_details')->where('ScheduleID',$sched_id)->where('ST_CODE',$st_code)->where('StatePHASE_NO',$phaseno)->where('CONST_TYPE',$cons_type)->orderBy('CONST_NO', 'ASC')->get();
              return $rec;
            }
     function checkelection_details($st_code,$consno,$sched_id)
            {  
              $rec =DB::table('m_election_details')->where('ScheduleID',$sched_id)->where('ST_CODE',$st_code)->where('CONST_NO',$consno)->first();
               
              if(isset($rec))
                return 1;
              else
                 return 0;
            }
    function getelectionbystate($st_code,$con_type)
            {
              $rec =DB::table('m_election_details')->where('ST_CODE',$st_code)->where('CONST_TYPE',$con_type)->orderBy('StatePHASE_NO', 'ASC')->orderBy('CONST_NO', 'ASC')->get();
              return $rec;
            }
    function getschedule()
            {
              $schedule = DB::table('m_schedule')->where('CURRENTELECTION','Y')->orderBy('ScheduleID', 'asc')->get();
              return $schedule;
            }
    function listcurrentelectionstate()
            {
             $list = DB::table('m_election_details')->select('ST_CODE') 
                      ->groupBy('m_election_details.ST_CODE')->orderBy('ST_CODE','ASC')->get();
              return $list;
            }
    function listcurrentelectionphase()
            {
             $list = DB::table('m_election_details')->select('StatePHASE_NO') 
                      ->groupBy('m_election_details.StatePHASE_NO')->orderBy('StatePHASE_NO','ASC')->get();
              return $list;
            }
    function getallelectionbyid()
            {
             $list = DB::table('m_election_details')
                      ->select('ELECTION_TYPEID','CONST_TYPE','ELECTION_TYPE','ST_CODE') 
                      ->groupBy('m_election_details.ELECTION_TYPEID','CONST_TYPE','ELECTION_TYPE','ST_CODE')
                      ->orderBy('ELECTION_TYPEID','ASC')->get();
             return $list;
            }
    function listelectiontype()
            {

             $list = DB::table('m_election_details')
                      ->select('ELECTION_TYPEID','CONST_TYPE','ELECTION_TYPE') 
                      ->groupBy('m_election_details.ELECTION_TYPEID','CONST_TYPE','ELECTION_TYPE')
                      ->orderBy('ELECTION_TYPEID','ASC')->get();
             return $list;
            }

              /* Sanjay query for eci dashboard start */
  function getstatebyelectionid($elec_id)
  {
      $list = DB::table('m_election_details')
                      ->select('ELECTION_TYPEID','CONST_TYPE','ELECTION_TYPE','ST_CODE') 
                      ->groupBy('m_election_details.ELECTION_TYPEID','CONST_TYPE','ELECTION_TYPE')
                      ->orderBy('ELECTION_TYPEID','ASC')
            ->where('ELECTION_ID','=',$elec_id)
            ->first();
             return $list;
  }
  function gettotaldistbystate($st_code){
    $list = DB::table('m_district')->where('ST_CODE','=',$st_code)->count();
    return $list;           
  }
  function gettotalacbystate($st_code){
    $list = DB::table('m_ac')->where('ST_CODE','=',$st_code)->count();
    return $list;           
  }
  function gettotalpsbystate($st_code){
    $list = DB::table('polling_station')->where('ST_CODE','=',$st_code)->count();
    return $list;           
  }
  function gettotalelectorsbystate($st_code,$elec_id){
    $list = DB::table('electors_cdac')->where('ST_CODE','=',$st_code)->where('election_id','=',$elec_id)->sum('electors_total');
    return $list;           
  }
  /* Sanjay query for eci dashboard ends */
}