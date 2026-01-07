<?php 
namespace App\models\Counting;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class BoothCountingTableModel extends Model
{

  public static function getallassigntable($data=array()){
     $results=[
         'assigntable'=>'',
         'countassigntable'=>0,
     ];
     $sql_raw = "GROUP_CONCAT(table_no) AS table_no,st_code,ac_no";
     $sql = DB::table('counting_users_table_details')->selectRaw($sql_raw);

    if(!empty($data['st_code'])){
               $sql->where("st_code", $data['st_code']);
    }

    if(!empty($data['election_id'])){
         $sql->where("election_id",$data['election_id']);
     }

    if(!empty($data['ac_no'])){
        $sql->where("ac_no",$data['ac_no']);
    }

   $sql->where("deleted",'0');
   $sql->groupBy('ac_no');
   $query = $sql->get();
 //dd($query);

      if(isset($query)) {
        foreach($query as $dat){
                $countassign =count(explode(",",$dat->table_no));
                $results['countassigntable'] +=  $countassign;
           }
      }

       return $results;
  }

  public static function get_reports($data = array()){
//dd($data);
    $election_id = Auth::user()->election_id;

    if(!empty($data['eci'])){ 
    $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE  AS st_code,(select count(ps.ps_no) from polling_station as ps where ps.st_code = ms.ST_CODE) AS total_ps, SUM(tm.total_no_tables) AS total_tables,SUM(rm.`scheduled_round`) AS total_rounds";
   }

    if(!empty($data['state'])){  
       $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE  AS st_code,ac.`AC_NAME` AS const_name, ac.`AC_NO` AS const_no, SUM(tm.total_no_tables) AS total_tables,(select count(ps.ps_no) from polling_station as ps where ps.st_code = ms.ST_CODE and ps.ac_no = e.const_no) AS total_ps, Sum(rm.`scheduled_round`) AS total_rounds";
    }
    if(!empty($data['state']) && !empty($data['district'])){  
       $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE  AS st_code,ac.`AC_NAME` AS const_name, ac.`AC_NO` AS const_no, tm.total_no_tables AS total_tables,tm.`total_no_ps` AS total_ps, rm.`scheduled_round` AS total_rounds";
    }

    if(!empty($data['ac_no'])){
      $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE  AS st_code,ac.`AC_NAME` AS const_name, ac.`AC_NO` AS const_no, tm.total_no_tables AS total_tables,(select count(ps.ps_no) from polling_station as ps where ps.st_code = ms.ST_CODE and ps.ac_no = e.const_no) AS total_ps, rm.`scheduled_round` AS total_rounds";
    }

    $sql = DB::table('m_election_details as e')
    ->leftjoin('m_state as ms',[
          ['ms.ST_CODE', '=','e.ST_CODE']
    ])
    ->leftjoin('table_master as tm',[
          ['tm.ac_no', '=','e.CONST_NO'],
          ['tm.st_code','=','e.ST_CODE'],
          ['tm.election_id','=','e.ELECTION_ID'],
    ])
    ->leftjoin('m_ac as ac',[
          ['ac.AC_NO', '=','e.CONST_NO'],
          ['ac.ST_CODE', '=','e.ST_CODE'],
    ])
    ->leftjoin('round_master as rm',[
          ['rm.ac_no', '=','e.CONST_NO'],
          ['rm.st_code', '=','e.ST_CODE'],
    ]);

    $sql->selectRaw($sql_raw);

    if(!empty($data['state'])){
      $sql->where("e.ST_CODE", $data['state']);
    }

    if(!empty($data['district'])){
      $sql->where("ac.DIST_NO_HDQTR", $data['district']);
    }

    if(!empty($data['ac_no'])){
      $sql->where("e.CONST_NO", $data['ac_no']);
    }

    if(!empty($data['group_by'])){

        if($data['group_by']=='ac'){
            $sql->groupBy("e.CONST_NO");
        }

        if($data['group_by']=='state'){
            $sql->groupBy("e.ST_CODE")->groupBy("e.CONST_NO");
        }

        if($data['group_by']=='eci'){
            $sql->groupBy("e.ST_CODE");
        }
    }

    if(!empty($data['order_by'])){
        if($data['order_by']=='ac'){
            $sql->orderByRaw("ms.ST_NAME, ac.ac_no, ac.AC_NAME ASC");
        }elseif ($data['order_by']=='dist_no') {
          $sql->orderByRaw("ms.ST_NAME, ac.DIST_NO_HDQTR, ac.AC_NAME ASC");
        }
    }else{
        $sql->orderByRaw("ms.ST_NAME, ac.ac_no, ac.AC_NAME ASC");
    }

    $sql->where('e.CONST_TYPE','AC');
    $sql->where('e.election_status','1');
    $sql->where('e.ELECTION_ID',$election_id);
 
    $query = $sql->get();
 
    return $query;

  }

}  // end class

 