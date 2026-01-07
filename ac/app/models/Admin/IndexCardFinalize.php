<?php 
namespace App\models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;
class IndexCardFinalize extends Model
{
  
   protected $table = 'electors_cdac_other_information';
 

   public static function get_reports($data = array()){
         
         $sql_raw = "w.finalize, p.AC_NO AS acno,  p.AC_NAME AS ac_name, s.ST_NAME AS st_name,
                    s.ST_CODE AS st_code,IF(w.finalize_by_ro='1','Yes','No') AS FinalizeRo,IF(w.finalize_by_ceo='1','Yes','No') AS FinalizeCeo,
                    IF(finalize!='1','Yes','No') AS Finalize,IF(cf.indexcard_finalize='1','Yes','No') AS NominationFinalize,IF(cuf.status='1','Yes','No') AS CountingFinalize";

        $sql = DB::table('m_ac as p')
		->join('candidate_finalized_ac as cf',[
              ['p.AC_NO', '=','cf.const_no'],
              ['p.ST_CODE', '=','cf.st_code'],
        ])
		->join('winning_leading_candidate as cuf',[
              ['p.AC_NO', '=','cuf.ac_no'],
              ['p.ST_CODE', '=','cuf.st_code'],
        ])
        ->leftjoin('electors_cdac_other_information as w',[
              ['p.AC_NO', '=','w.ac_no'],
              ['p.ST_CODE', '=','w.st_code'],
        ])
        ->leftjoin('m_election_details as med',[
              ['p.ST_CODE', '=','med.ST_CODE'],
              ['p.AC_NO', '=','med.CONST_NO'],
            
        ])
        ->join('m_state as s',[
              ['p.ST_CODE', '=','s.ST_CODE']
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("s.ST_CODE", $data['state']);
        }

        if(!empty($data['ac_no'])){
          $sql->where("p.AC_NO", $data['ac_no']);
        }


       $sql->where("med.CONST_TYPE", "AC");
       $sql->where("med.election_status", '!=','0');
     
       

        //$sql->whereRaw("p.PC_NO != 8 AND s.ST_CODE != 'S22'");

        $sql->orderByRaw("p.ST_CODE, p.AC_NO ASC");
        $sql->groupBy(DB::raw('p.AC_NO'));

        $query = $sql->get();
     
        return $query;

    }
	
	
	public static function get_states($data = array()){

    $sql_raw = "SELECT COUNT(p.AC_NO) AS total_ac, COUNT(IF(w.finalize='1',1,NULL)) AS finalize,COUNT(IF(w.finalize_by_ceo='1',1,NULL)) AS FinalizeCeo, SUM(IF(cf.indexcard_finalize='1',1,0)) AS NominationFinalize,SUM(IF(cuf.status='1',1,0)) AS CountingFinalize, s.ST_NAME AS st_name,s.ST_CODE AS st_code
		FROM m_ac AS p INNER JOIN candidate_finalized_ac AS cf ON (p.AC_NO = cf.const_no AND p.ST_CODE = cf.st_code) INNER JOIN
		 (SELECT ST_CODE,ac_no,status  FROM winning_leading_candidate  GROUP BY st_code,ac_no) AS cuf ON (p.AC_NO = cuf.ac_no AND p.ST_CODE = cuf.st_code) LEFT JOIN electors_cdac_other_information AS w ON (p.AC_NO = w.ac_no AND p.ST_CODE = w.st_code) LEFT JOIN m_election_details AS med ON (p.ST_CODE = med.ST_CODE AND p.AC_NO = med.CONST_NO) INNER JOIN m_state AS s ON (p.ST_CODE = s.ST_CODE)
		WHERE med.CONST_TYPE = 'AC' AND med.election_status != 0"; 


		if(!empty($data['state'])){
		  $state = $data['state'];		  
		  $sql_raw .=" AND s.ST_CODE = '$state'";		  
        }

        if(!empty($data['dist_no'])){
		  $dist_no = $data['dist_no'];		  
		  $sql_raw .=" AND p.DIST_NO_HDQTR = $dist_no";
        }
		
		if(!empty($data['ac_no'])){
		  $ac_no = $data['ac_no'];		  
		  $sql_raw .=" AND p.AC_NO = $ac_no";
        }


		$sql_raw .=" GROUP BY s.ST_CODE ORDER BY s.ST_CODE ASC";

        $sql = DB::select($sql_raw);

        $query = $sql;
		
		
		//echo '<pre>'; print_r($query); die;
     
        return $query;

    }


}