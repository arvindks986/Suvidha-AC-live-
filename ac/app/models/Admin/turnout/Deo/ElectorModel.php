<?php 
namespace App\models\Admin\turnout\Deo;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Auth;

class ElectorModel extends Model
{
  
   protected $table = 'electors_cdac';

   public static function get_sum($data = array()){

        $percent = 0;

        $election_id = Auth::user()->election_id;

        $sql_raw = "ROUND(SUM(electors_cdac.voter_total)/SUM(electors_cdac.electors_total)*100,2) as voter_total";
    
        $sql = ElectorModel::join('pd_scheduledetail as sd1',[
            ['sd1.st_code', '=','electors_cdac.st_code'],
            ['sd1.ac_no', '=','electors_cdac.ac_no'],
        ])
        ->join('m_election_details as e',[
            ['e.ST_CODE', '=','electors_cdac.st_code'],
            ['e.CONST_NO', '=','electors_cdac.ac_no']
        ]);
        if(!empty($data['dist_no'])){
          $sql->join('e',[
            ['e.ST_CODE', '=','m_ac.ST_CODE'],
            ['e.CONST_NO', '=','m_ac.AC_NO']
        ]);
        }
        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("electors_cdac.st_code", $data['state']);
        }

        if(!empty($data['ac_no'])){
          $sql->where("electors_cdac.ac_no", $data['ac_no']);
        }

        if(!empty($data['dist_no'])){
          $sql->where('m_ac.DIST_NO_HDQTR',$data['dist_no']);
        }

        if(!empty($data['phase'])){
          $sql->where("sd1.scheduleid", $data['phase']);
        }

        $sql->where('e.CONST_TYPE','AC');
        $sql->where('e.election_status','1');
        $sql->where('e.ELECTION_ID',$election_id);

        
        if(!empty($data['group_by'])){
            if($data['group_by']=='ac_no'){
              $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
            }else if($data['group_by']=='state'){
              $sql->groupBy("electors_cdac.st_code");
            }else{
            }
        }else{
          $sql->groupBy("electors_cdac.st_code");
        }

        $query = $sql->first();

        if($query){
            $percent = $query->voter_total;
        }
        return $percent;

   }

}