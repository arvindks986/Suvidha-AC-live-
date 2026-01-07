<?php 
namespace App\models\Admin\turnout;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class TurnoutModel extends Model
{
  public  function get_scheduledetail($data = array()){
    
    $sql_raw = "id, election_id, st_code,ac_no,pc_no,electors_total, est_voters,updated_at,updated_at_finalize,est_poll_close,close_of_poll,pd_scheduleid, scheduleid, end_voter_male,end_voter_female,end_voter_other,end_voter_total, total_male,total_female,total_other,total,est_turnout_round1,est_turnout_round2, est_turnout_round3,est_turnout_round4,est_turnout_round5, est_turnout_total, ac_election,update_at_round3,update_at_round1,update_at_round2,update_at_round4, update_at_round5,update_at_final,update_device_round1,update_device_round2, update_device_round3,update_device_round4,update_device_round5, update_device_final, end_of_poll_finalize,missed_status_round1,missed_status_round2,missed_status_round3,missed_status_round4,missed_status_round5,missed_status_round6
	,modification_status_round1,modification_status_round2,modification_status_round3,modification_status_round4,modification_status_round5,modification_status_round6";
    
    $sql = DB::table('pd_scheduledetail')->selectRaw($sql_raw);

    if(!empty($data['election_id'])){
      $sql->where("election_id", $data['election_id']);
    }
    if(!empty($data['st_code'])){
      $sql->where("st_code", $data['st_code']);
    }
    if(!empty($data['pc_no'])){
      $sql->where("pc_no", $data['pc_no']);
    }
    if(!empty($data['ac_no'])){
      $sql->where("ac_no", $data['ac_no']);
    }
    $query = $sql->first();
     return $query;
    
    }

  public  function get_schedulemaster($data = array()){
    
    $sql_raw = "pd_scheduleid, election_id, st_code,ac_no,pc_no,district_no, const_type,schedule_id, state_phase_no,electionid,election_type_id,year, month, created_at,ceo_finalize";
    $sql = DB::table('pd_schedulemaster')->selectRaw($sql_raw);

    if(!empty($data['election_id'])){
      $sql->where("electionid", $data['election_id']);
    }
    if(!empty($data['st_code'])){
      $sql->where("st_code", $data['st_code']);
    }
    if(!empty($data['pc_no'])){
      $sql->where("pc_no", $data['pc_no']);
    }
    if(!empty($data['ac_no'])){
      $sql->where("ac_no", $data['ac_no']);
    }
     if(!empty($data['const_type'])){
      $sql->where("const_type", $data['const_type']);
      }

      $query = $sql->first();
     return $query;
    
    }
   public static function getcdacelectorsdetails($data = array()){
               $sql_raw = "*";
               $sql = DB::table('electors_cdac')->selectRaw($sql_raw);

                if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                }
                if(!empty($data['st_code'])){
                  $sql->where("st_code", $data['st_code']);
                }
                if(!empty($data['ac_no'])){
                  $sql->where("ac_no", $data['ac_no']);
                }
                $query = $sql->first();
                  return $query; 
           }
  public static function get_total_roundnewac($data = array()){
               $sql_raw = "*";
               $sql = DB::table('pd_scheduledetail')->selectRaw($sql_raw);

                if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                }
                if(!empty($data['st_code'])){
                  $sql->where("st_code", $data['st_code']);
                }
                if(!empty($data['ac_no'])){
                  $sql->where("ac_no", $data['ac_no']);
                }
              $query = $sql->first();
              return $query; 
         }

   public function get_total_roundac($data = array()){
    $total = [
        'round_1_total' => 0,
        'round_2_total' => 0,
        'round_3_total' => 0,
        'round_4_total' => 0,
        'round_5_total' => 0,
        'round_end_total' => 0,
        'total_voter_male'      => 0,
        'total_voter_female'    => 0,
        'total_voter_other'     => 0,
    ];
    $sql = "IFNULL(SUM(round1_voter_male+round2_voter_male+round3_voter_male+round4_voter_male+round5_voter_male+end_voter_male),0) as total_voter_male,IFNULL(SUM(round1_voter_female+round2_voter_female+round3_voter_female+round4_voter_female+round5_voter_female+end_voter_female),0) as total_voter_female, IFNULL(SUM(round1_voter_other+round2_voter_other+round3_voter_other+round4_voter_other+round5_voter_other+end_voter_other),0) as total_voter_other, IFNULL(SUM(round1_voter_total),0) as round_1_total, IFNULL(SUM(round2_voter_total),0) as round_2_total, IFNULL(SUM(round3_voter_total),0) as round_3_total, IFNULL(SUM(round4_voter_total),0) as round_4_total, IFNULL(SUM(round5_voter_total),0) as round_5_total, IFNULL(SUM(end_voter_total),0) as round_end_total";
    $sql = DB::table('pd_scheduledetail as ps')->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ps.st_code',$data['st_code']);
    }
    if(!empty($data['const_no'])){
      $sql->where('ps.ac_no',$data['const_no']);
    }
    $sql->where('ps.ac_no','!=','0')->where('ps.ac_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = $query;
    }
    return $total;
  }

              
    public function get_elector_totalac($data = array()){
        $result = [
                  'electors_male' => 0,
                  'electors_female' => 0,
                  'electors_other' => 0,
                  'electors_total' => 0,
                  ];
        $sql_raw = "IFNULL(electors_male,0) as electors_male, IFNULL(electors_female,0) as electors_female, IFNULL(electors_other,0) as electors_other, IFNULL(electors_total,0) as electors_total";
          $sql= DB::table('electors_cdac')->selectRaw($sql_raw);
        if(!empty($data['st_code'])){
            $sql->where('st_code',$data['st_code']);
            }
        if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
            }
        if(!empty($data['election_id'])){
            $sql->where('election_id',$data['election_id']);
            }
        $record = $sql->first();
        if($record){
            $result = [
                'electors_male' => $record->electors_male,
                'electors_female' => $record->electors_female,
                'electors_other' => $record->electors_other,
                'electors_total' => $record->electors_total,
            ];
        }
        return  $result;
     
    }     //  FROM ``";



public  function get_scheduletime($data = array()){
    
    $sql_raw = "*";
    $sql = DB::table('pd_schedule_estimated')->selectRaw($sql_raw);

    if(!empty($data['election_id'])){
      $sql->where("election_id", $data['election_id']);
    }
    if(!empty($data['st_code'])){
      $sql->where("st_code", $data['st_code']);
      }
	  
	if(!empty($data['phase_no'])){
      $sql->where("phase_no", $data['phase_no']);
      }  
	  

      $query = $sql->first();
      if(isset($query))
           return $query;
      else
           return false;
    
    }

public  function check_turnout_exempted($data = array()){
      $sql_raw = "*";
      $sql = DB::table('pd_schedule_exempted')->selectRaw($sql_raw);
       if(!empty($data['st_code'])){
            $sql->where('st_code',$data['st_code']);
            }
        if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
            }
        if(!empty($data['election_id'])){
            $sql->where("election_id", $data['election_id']);
          }
        if(!empty($data['phase_no'])){
          $sql->where("phase_no", $data['phase_no']);
          }
          $sql->where("status",'1');

        $query = $sql->first();
      if(isset($query))
           return 1;
      else
           return 0;
    
    }

    public  function check_turnout_entry_enable($data = array()){
      $sql_raw = "is_est_entry_allow";
      $sql = DB::table('pd_scheduledetail')->selectRaw($sql_raw);
       if(!empty($data['st_code'])){
            $sql->where('st_code',$data['st_code']);
            }
        if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
            }
        if(!empty($data['election_id'])){
            $sql->where("election_id", $data['election_id']);
          }
        if(!empty($data['scheduleid'])){
          $sql->where("scheduleid", $data['phase_no']);
          }
          $sql->where("is_est_entry_allow",'1');

        $query = $sql->first();
      if(isset($query))
           return 1;
      else
           return 0;
    
    }

    public static function get_turn_out_data($data=array()){
      $sql_raw = "est_turnout_total, electors_total, est_voters";
      $sql = DB::table('pd_scheduledetail')->selectRaw($sql_raw);
       if(!empty($data['st_code'])){
            $sql->where('st_code',$data['st_code']);
            }
        if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
            }
        if(!empty($data['election_id'])){
            $sql->where("election_id", $data['election_id']);
          }
        if(!empty($data['scheduleid'])){
          $sql->where("scheduleid", $data['phase_no']);
          }

        $query = $sql->first();
      return $query;
    }
}  // end class

 