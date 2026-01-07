<?php 

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Auth;

class PollDayComparisionModel extends Model
{
  

  public static function old_percentage($data = array()){
	  
      $election_id = Auth::user()->election_id;

      $sql = DB::table('m_election_details')->join('previous_election_details',[
              ['m_election_details.ST_CODE', '=','previous_election_details.st_code'],
              ['m_election_details.CONST_NO', '=','previous_election_details.ac_no']]);

        if(!empty($data['st_code'])){
           $sql->where('m_election_details.ST_CODE',$data['st_code']);
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

        $sql->where('m_election_details.CONST_TYPE','AC');
        $sql->where('m_election_details.election_status','1');
        $sql->where('m_election_details.ELECTION_ID',$election_id);

        return $sql->orderBy('previous_election_details.ac_no', 'ASC')
              ->select('previous_election_details.*')->first();
    }


	   public static function get_average_sum($data = array()){

      $election_id = Auth::user()->election_id;

      $sql_raw  = "IFNULL(ROUND(AVG(est_total_turnout),2),0) as total_percent";
      $sql    = DB::table('previous_election_details as sd1')->join('m_election_details as e',[
            ['e.ST_CODE', '=','sd1.st_code'],
            ['e.CONST_NO', '=','sd1.ac_no']
        ])
      ->selectRaw($sql_raw);
	  
	  
	  if(!empty($data['election_type'])){
            $sql->where('e.ELECTION_TYPEID',$data['election_type']);
        }
	  
	  
      if(!empty($data['state'])){
        $sql->where("sd1.st_code", $data['state']);
      }

      if(!empty($data['phase'])){
        $sql->where("e.PHASE_NO", $data['phase']);
      }

      if(!empty($data['pc_no'])){
        $sql->where("sd1.pc_no", $data['pc_no']);
      }

      $sql->where('e.CONST_TYPE','AC');
      $sql->where('e.election_status','1');
      $sql->where('e.ELECTION_ID',$election_id);

      
       $sql->groupBy("sd1.st_code");
     

      $query = $sql->first();
	  
      return ($query)?$query->total_percent:0;

    }
	

}