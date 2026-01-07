<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class Pollday extends Model
{
    //
  //not delete  
  function get_total_by_state($data = array())
  {

    $result = [
            'round_1_m'              => 0,
            'round_1_f'              => 0,
            'round_1_o'              => 0,
            'round_1_t'              => 0,
            'round_2_m'              => 0,
            'round_2_f'              => 0,
            'round_2_o'              => 0,
            'round_2_t'              => 0,
            'round_3_m'              => 0,
            'round_3_f'              => 0,
            'round_3_o'              => 0,
            'round_3_t'              => 0,
            'round_4_m'              => 0,
            'round_4_f'              => 0,
            'round_4_o'              => 0,
            'round_4_t'              => 0,
            'round_5_m'              => 0,
            'round_5_f'              => 0,
            'round_5_o'              => 0,
            'round_5_t'              => 0,
            'round_end_m'              => 0,
            'round_end_f'              => 0,
            'round_end_o'              => 0,
            'round_end_t'              => 0,
    ];  
    $sql = "IFNULL(SUM(round1_voter_male),0) as round_1_m, IFNULL(SUM(round1_voter_female),0) as round_1_f, IFNULL(SUM(round1_voter_other),0) as round_1_o, IFNULL(SUM(round1_voter_total),0) as round_1_t, IFNULL(SUM(round2_voter_male),0) as round_2_m, IFNULL(SUM(round2_voter_female),0) as round_2_f, IFNULL(SUM(round2_voter_other),0) as round_2_o, IFNULL(SUM(round2_voter_total),0) as round_2_t, IFNULL(SUM(round3_voter_male),0) as round_3_m, IFNULL(SUM(round3_voter_female),0) as round_3_f, IFNULL(SUM(round3_voter_other),0) as round_3_o, IFNULL(SUM(round3_voter_total),0) as round_3_t, IFNULL(SUM(round4_voter_male),0) as round_4_m, IFNULL(SUM(round4_voter_female),0) as round_4_f, IFNULL(SUM(round4_voter_other),0) as round_4_o, IFNULL(SUM(round4_voter_total),0) as round_4_t, IFNULL(SUM(round5_voter_male),0) as round_5_m, IFNULL(SUM(round5_voter_female),0) as round_5_f, IFNULL(SUM(round5_voter_other),0) as round_5_o, IFNULL(SUM(round5_voter_total),0) as round_5_t, IFNULL(SUM(end_voter_male),0) as round_end_m, IFNULL(SUM(end_voter_female),0) as round_end_f, IFNULL(SUM(end_voter_other),0) as round_end_o, IFNULL(SUM(end_voter_total),0) as round_end_t";

    $sql = DB::table('pd_scheduledetail as ps')->join('pd_schedulemaster','pd_schedulemaster.pd_scheduleid','=','ps.pd_scheduleid')->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ps.st_code',$data['st_code']);
    }
    $sql->where('ps.pc_no','!=','0')->where('ps.pc_no','!=',NULL);
    $query = $sql->first();

    if($query){
      $result = [
            'round_1_m'              => $query->round_1_m,
            'round_1_f'              => $query->round_1_f,
            'round_1_o'              => $query->round_1_o,
            'round_1_t'              => $query->round_1_t,
            'round_2_m'              => $query->round_2_m,
            'round_2_f'              => $query->round_2_f,
            'round_2_o'              => $query->round_2_o,
            'round_2_t'              => $query->round_2_t,
            'round_3_m'              => $query->round_3_m,
            'round_3_f'              => $query->round_3_f,
            'round_3_o'              => $query->round_3_o,
            'round_3_t'              => $query->round_3_t,
            'round_4_m'              => $query->round_4_m,
            'round_4_f'              => $query->round_4_f,
            'round_4_o'              => $query->round_4_o,
            'round_4_t'              => $query->round_4_t,
            'round_5_m'              => $query->round_5_m,
            'round_5_f'              => $query->round_5_f,
            'round_5_o'              => $query->round_5_o,
            'round_5_t'              => $query->round_5_t,
            'round_end_m'              => $query->round_end_m,
            'round_end_f'              => $query->round_end_f,
            'round_end_o'              => $query->round_end_o,
            'round_end_t'              => $query->round_end_t,    
        ];  
    }

    return $result;

  }

  public function get_total_round($data = array()){
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
    $sql = "IFNULL(SUM(round1_voter_male+round2_voter_male+round3_voter_male+round4_voter_male+round5_voter_male+end_voter_male),0) as total_voter_male,IFNULL(SUM(round1_voter_female+round2_voter_female+round3_voter_female+round4_voter_female+round5_voter_female+end_voter_female),0) as total_voter_female, IFNULL(SUM(round1_voter_other+round2_voter_other+round3_voter_other+round4_voter_other+round5_voter_other+end_voter_other),0) as total_voter_other, IFNULL(SUM(round1_voter_total),0) as round_1_total, IFNULL(SUM(round2_voter_total),0) as round_2_total, IFNULL(SUM(round3_voter_total),0) as round_3_total, IFNULL(SUM(round4_voter_total),0) as round_4_total, IFNULL(SUM(round5_voter_total),0) as round_5_total, IFNULL(SUM(end_voter_total),0) as round_end_total,IFNULL(SUM(end_voter_total),0) as round_end_total,IFNULL(SUM(end_voter_total),0) as round_end_total";
    $sql = DB::table('pd_scheduledetail as ps')->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ps.st_code',$data['st_code']);
    }
    if(!empty($data['const_no'])){
      $sql->where('ps.pc_no',$data['const_no']);
    }
    $sql->where('ps.pc_no','!=','0')->where('ps.pc_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = $query;
    }
    return $total;
  }
public function get_total_roundnew($data = array()){
    
     // $record = DB::table('pd_scheduledetail')->where('st_code', $st)->where('pc_no', $const)->first();
     //  return  $record;
    $total = [
        'total_male' => 0,
        'total_female' => 0,
        'total' => 0,
        'total_other' => 0,
         
    ];
    $sql = "IFNULL(SUM(total_male),0) as total_voter_male,IFNULL(SUM(total_female),0) as total_voter_female, IFNULL(SUM(total_other),0) as total_voter_other, IFNULL(SUM(total),0) as total";
    $sql = DB::table('pd_scheduledetail as ps')->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ps.st_code',$data['st_code']);
    }
    if(!empty($data['const_no'])){
      $sql->where('ps.pc_no',$data['const_no']);
    }
    $sql->where('ps.pc_no','!=','0')->where('ps.pc_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = $query;
    }
    return $total;
  }
  public function get_total_roundnewac($st,$const){
    
     $record = DB::table('pd_scheduledetail')->where('st_code', $st)->where('ac_no', $const)->first();
      return  $record;
  }

  public function get_elector_total($data = array()){
    $total = [
        'gen_m' => 0,
        'gen_f' => 0,
        'gen_o' => 0,
        'gen_t' => 0,
    ];
    $sql = "IFNULL(SUM(electors_male),0) as gen_m, IFNULL(SUM(electors_female),0) as gen_f, IFNULL(SUM(electors_other),0) as gen_o, IFNULL(SUM(electors_total),0) as gen_t";
    $sql = DB::table('electors_cdac as ed')->selectRaw($sql);
    if(!empty($data['st_code'])){
      $sql->where('ed.st_code',$data['st_code']);
    }
    if(!empty($data['const_no'])){
      $sql->where('ed.pc_no',$data['const_no']);
    }
    if(!empty($data['year'])){
      $sql->where('ed.year',$data['year']);
    }
    $sql->where('ed.pc_no','!=','0')->where('ed.pc_no','!=',NULL);
    $query = $sql->first();
    if($query){
        $total = $query;
    }
    return $total;
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


    public function get_elector_totalac($st,$const,$year){
        $result = [
            'electors_male' => 0,
            'electors_female' => 0,
            'electors_other' => 0,
            'electors_total' => 0,
        ];
        $sql = "IFNULL(electors_male,0) as electors_male, IFNULL(electors_female,0) as electors_female, IFNULL(electors_other,0) as electors_other, IFNULL(electors_total,0) as electors_total";
        $record = DB::table('electors_cdac')->where('st_code', $st)->where('ac_no', $const)->where('year', $year)->selectRaw($sql)->first();
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
     public function get_maxtotal_voter($st,$const)
            {
             $sql = "GREATEST(round1_voter_total,round2_voter_total,round3_voter_total,round4_voter_total,round5_voter_total,end_voter_total) as max_voter_total";
             $record = DB::table('pd_scheduledetail')->where('st_code', $st)->where('ac_no', $const)->selectRaw($sql)->first();
                 if($record){
                    $result = [
                        'max_voter_total' => $record->max_voter_total,
                      ];
                }
                return  $result;  
            }
     public function get_maxmale_voter($st,$const)
            {
             $sql = "GREATEST(round1_voter_male,round2_voter_male,round3_voter_male,round4_voter_male,round5_voter_male,end_voter_male) as max_voter_male";
             $record = DB::table('pd_scheduledetail')->where('st_code', $st)->where('ac_no', $const)->selectRaw($sql)->first();
                 if($record){
                    $result = [
                        'max_voter_male' => $record->max_voter_male,
                      ];
                }
                return  $result;  
            }
     public function get_maxfemale_voter($st,$const)
            {
             $sql = "GREATEST(round1_voter_female,round2_voter_female,round3_voter_female,round4_voter_female,round5_voter_female,end_voter_female) as max_voter_female";
             $record = DB::table('pd_scheduledetail')->where('st_code', $st)->where('ac_no', $const)->selectRaw($sql)->first();
                 if($record){
                    $result = [
                        'max_voter_female' => $record->max_voter_female,
                      ];
                }
                return  $result;  
            }
     public function get_maxother_voter($st,$const)
            {
             $sql = "GREATEST(round1_voter_other,round2_voter_other,round3_voter_other,round4_voter_other,round5_voter_other,end_voter_other) as max_voter_other";
             $record = DB::table('pd_scheduledetail')->where('st_code', $st)->where('ac_no', $const)->selectRaw($sql)->first();
                 if($record){
                    $result = [
                        'max_voter_other' => $record->max_voter_other,
                      ];
                }
                return  $result;  
            }
    public function est_pcwiseturnout_total($data = array()){
            $total = [
                'est_turnout_total' => 0,
               
            ];
            $sql = "IFNULL(SUM(est_turnout_total),0) as est_turnout_total";
            $sql = DB::table('pd_scheduledetail as ed')->selectRaw($sql);
            if(!empty($data['st_code'])){
              $sql->where('ed.st_code',$data['st_code']);
            }
            if(!empty($data['const_no'])){
              $sql->where('ed.pc_no',$data['const_no']);
            }
            $sql->where('ed.pc_no','!=','0')->where('ed.pc_no','!=',NULL);
            $query = $sql->first();
            if($query){
                $total = $query;
            }
            return $total;
          }

     public static function get_reports($data = array()){

        $sql_raw = "IFNULL(ROUND(AVG(est_turnout_round1),2),0) as est_total_round1, IFNULL(ROUND(AVG(est_turnout_round2),2),0) as est_total_round2, IFNULL(ROUND(AVG(est_turnout_round3),2),0) as est_total_round3, IFNULL(ROUND(AVG(est_turnout_round4),2),0) as est_total_round4, IFNULL(ROUND(AVG(est_turnout_round5),2),0) as est_total_round5, IFNULL(ROUND(AVG(close_of_poll),2),0) as close_of_poll, IFNULL(ROUND(AVG(est_turnout_total),2),0) as est_total, COUNT(*) as total_record, IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percentage, pc.PC_NO as pc_no, pc.PC_NAME as pc_name, pc.st_code, sd1.ac_no as ac_no";

        $sql = DB::table('pd_scheduledetail as sd1')
        ->join('pd_schedulemaster as sm1',[
              ['sd1.pd_scheduleid', '=','sm1.pd_scheduleid']
        ])
        ->join('m_pc as pc',[
              ['pc.PC_NO', '=','sd1.pc_no'],
              ['pc.ST_CODE', '=','sd1.st_code'],
        ])
        ->leftjoin('m_state as state',[
              ['state.ST_CODE', '=','pc.ST_CODE']
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("sd1.st_code", $data['state']);
        }

        if(!empty($data['phase'])){
          $sql->where("sm1.schedule_id", $data['phase']);
        }

        if(!empty($data['pc_no'])){
          $sql->where("sm1.pc_no", $data['pc_no']);
        }

        if(!empty($data['group_by']) && in_array($data['group_by'],['pc_no','ac_no'])){
            if($data['group_by']=='pc_no'){
                $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
            }else if($data['group_by']=='ac_no'){
                $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
            }
        }else{
            $sql->groupBy("sd1.st_code");
        }

        if(!empty($data['order_by']) && in_array($data['order_by'],['pc_no','ac_no'])){
            if($data['order_by']=='pc_no'){
                $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
            }else if($data['order_by']=='ac_no'){
                $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME, sd1.ac_no ASC");
            }
        }else{
            $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
        }

        $query = $sql->get();
     
        return $query;

    }

    public static function get_average_sum($data = array()){

      $sql_raw  = "IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percent";
      $sql    = DB::table('pd_scheduledetail as sd1')->selectRaw($sql_raw);
      if(!empty($data['state'])){
        $sql->where("sd1.st_code", $data['state']);
      }

      if(!empty($data['phase'])){
        $sql->where("sd1.scheduleid", $data['phase']);
      }

      if(!empty($data['pc_no'])){
        $sql->where("sd1.pc_no", $data['pc_no']);
      }

      if(!empty($data['group_by']) && in_array($data['group_by'],['pc_no','ac_no'])){
          if($data['group_by']=='pc_no'){
            $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
          }else if($data['group_by']=='ac_no'){
            $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
          }else{
          $sql->groupBy("sd1.st_code");
        }
      }

      $query = $sql->first();
      return ($query)?$query->total_percent:0;

    }
}