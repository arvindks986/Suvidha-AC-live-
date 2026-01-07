<?php 
namespace App\models\Counting;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class MissingFlagsReportModel extends Model
{

  public static function get_reports($data = array()){

    $election_id = Auth::user()->election_id;

    if(!empty($data['eci'])){
    $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE  AS st_code,ac.`AC_NAME` AS const_name, ac.`AC_NO` AS const_no,IF(cfa.finalized_ac=1,'Yes','No') AS evm_finalized,IF(cfa.finalize_by_ro=1,'Yes','No') AS postal_finalize ";
   }

    if(!empty($data['state'])){
       $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE  AS st_code,ac.`AC_NAME` AS const_name, ac.`AC_NO` AS const_no,IF(cfa.finalized_ac=1,'Yes','No') AS evm_finalized,IF(cfa.finalize_by_ro=1,'Yes','No') AS postal_finalize ";
    }


    if(!empty($data['ac_no'])){
      $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE  AS st_code,ac.`AC_NAME` AS const_name, ac.`AC_NO` AS const_no,IF(cfa.finalized_ac=1,'Yes','No') AS evm_finalized,IF(cfa.finalize_by_ro=1,'Yes','No') AS postal_finalize ";
    }

    $sql = DB::table('m_election_details as e')
    ->leftjoin('m_state as ms',[
          ['ms.ST_CODE', '=','e.ST_CODE']
    ])
    ->leftjoin('counting_finalized_ac as cfa',[
          ['cfa.ac_no', '=','e.CONST_NO'],
          ['cfa.st_code','=','e.ST_CODE'],
    ])
    ->leftjoin('m_ac as ac',[
          ['ac.AC_NO', '=','e.CONST_NO'],
          ['ac.ST_CODE', '=','e.ST_CODE'],
    ]);

    $sql->selectRaw($sql_raw);

    if(!empty($data['state'])){
      $sql->where("e.ST_CODE", $data['state']);
    }

    if(!empty($data['district'])){
      $sql->where("e.dist_no", $data['district']);
    }

    if(!empty($data['ac_no'])){
      $sql->where("e.CONST_NO", $data['ac_no']);
    }

    /*if(!empty($data['group_by'])){

        if($data['group_by']=='ac'){
            $sql->groupBy("e.CONST_NO");
        }

        if($data['group_by']=='state'){
            $sql->groupBy("e.ST_CODE")->groupBy("e.CONST_NO");
        }

        if($data['group_by']=='eci'){
            $sql->groupBy("e.ST_CODE");
        }
    }*/
    $sql->orderByRaw("ms.ST_NAME, ac.ac_no, ac.AC_NAME ASC");
   /* if(!empty($data['order_by'])){
        if($data['order_by']=='ac'){
            $sql->orderByRaw("ms.ST_NAME, ac.ac_no, ac.AC_NAME ASC");
        }elseif ($data['order_by']=='dist_no') {
          $sql->orderByRaw("ms.ST_NAME, m_district.ac_no, m_district.AC_NAME ASC");
        }
    }else{
        $sql->orderByRaw("ms.ST_NAME, ac.ac_no, ac.AC_NAME ASC");
    }*/

    $sql->where('e.CONST_TYPE','AC');
    $sql->where('e.election_status','1');
    $sql->where('e.ELECTION_ID',$election_id);
 
    $query = $sql->get();
 
    return $query;

  }

}  // end class

 