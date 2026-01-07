<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Support\Facades\Auth;
use DB, Cache;

class ExemptPollingModel extends Model
{
  protected $table = 'boothapp_exempt_polling_station';

  //sum total stats
  public static function total_statics_count($filter = array()){

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.st_code","=","boothapp_exempt_polling_station.st_code"],
      ["m_election_details.CONST_NO","=","boothapp_exempt_polling_station.ac_no"],
    ])->where('CONST_TYPE','AC')->select("id");

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('boothapp_exempt_polling_station.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('boothapp_exempt_polling_station.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('boothapp_exempt_polling_station.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('boothapp_exempt_polling_station.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['is_started'])){
      $sql->whereNotNull('boothapp_exempt_polling_station.poll_start_datetime');
    }

    if(!empty($filter['is_connected'])){
      $sql->whereNotNull('boothapp_exempt_polling_station.poll_start_datetime')->whereNull('boothapp_exempt_polling_station.poll_end_datetime');
    }



    if(!empty($filter['is_end'])){
      $sql->whereNotNull('boothapp_exempt_polling_station.poll_start_datetime')->whereNotNull('boothapp_exempt_polling_station.poll_end_datetime');
    }

    if(!empty($filter['role_id'])){
      $sql->where('boothapp_exempt_polling_station.role_id',$filter['role_id']);
    }

    //$sql->where('tbl_poll_summary.row_status','A');
    return $sql->count("id");
   
  }
  public static function total_statics_sum($filter = array()){
    $data_stats = [
      'pro_turn_out'  => 0,
      'queue_voters'  => 0,
      'male_voters'   => 0,
      'female_voters' => 0,
      'other_voters'  => 0,
      'scan_qr'       => 0,
      'scan_srno'     => 0,
      'scan_epicno'   => 0,
      'scan_name'     => 0,
      'scan_mobile'   => 0,
    ];

    $sql = ExemptPollingModel::join("m_election_details",[
      ["m_election_details.st_code","=","boothapp_exempt_polling_station.st_code"],
      ["m_election_details.CONST_NO","=","boothapp_exempt_polling_station.ac_no"],
    ])->selectRaw("IFNULL(SUM(total_male),0) as total_male, IFNULL(SUM(total_female),0) as total_female, IFNULL(SUM(total_other),0) as total_other, IFNULL(SUM(total_total),0) as total_total,round_1_total,round_2_total,round_3_total,round_4_total,round_5_total");

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('boothapp_exempt_polling_station.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('boothapp_exempt_polling_station.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('boothapp_exempt_polling_station.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('boothapp_exempt_polling_station.ps_no',$filter['ps_no']);
    }

    $query =  $sql->first();
    if($query){
      if(count($query->round_1_total)>0){$round_1_total = $query->round_1_total;}else{$round_1_total = 0;}
      if(count($query->round_2_total)>0){$round_2_total = $query->round_2_total;}else{$round_2_total = 0;}
      if(count($query->round_3_total)>0){$round_3_total = $query->round_3_total;}else{$round_3_total = 0;}
      if(count($query->round_4_total)>0){$round_4_total = $query->round_4_total;}else{$round_4_total = 0;}
      if(count($query->round_5_total)>0){$round_5_total = $query->round_5_total;}else{$round_5_total = 0;}
      $data_stats = [
        'round_1_total'  => $round_1_total,
        'round_2_total'  => $round_2_total,
        'round_3_total'  => $round_3_total,
        'round_4_total'  => $round_4_total,
        'round_5_total'  => $round_5_total,
        'total_male'     => $query->total_male,
        'total_female'   => $query->total_female,
        'total_other'    => $query->total_other,
        'total_total'    => $query->total_total
      ];
    }
    
    return $data_stats;
  }


  public static function total_exempted_ps($data = array()){
     $election_id = Auth::user()->election_id;

      //STATE FUNCTION STARTS
      $sql_raw = "COUNT(ps.ps_no) as total_ps,COUNT(IF(psl.is_exempted=1,1,NULL)) as total_exempted";

     $sql = DB::table('boothapp_enable_acs as b')
        ->join('m_election_details as e',[
              ['b.st_code', '=','e.st_code'],
              ['b.ac_no', '=','e.CONST_NO'],
        ])
        ->leftjoin('boothapp_polling_station as ps',[
              ['b.ac_no', '=','ps.ac_no'],
              ['b.st_code', '=','ps.st_code'],
        ])
    ->leftjoin('boothapp_exempt_polling_station as psl',[
            ['ps.ST_CODE', '=','psl.st_code'],
      ['ps.ac_no', '=','psl.ac_no'],
      ['ps.ps_no', '=','psl.ps_no']]);
  
  
        $sql->selectRaw($sql_raw);

        $sql->where('e.CONST_TYPE', '=', 'AC');
        $sql->where('e.election_status', '=', '1');
        $sql->where('e.ELECTION_ID',$election_id);
      
        if(!empty($data['st_code'])){
          $sql->where("ps.st_code", $data['st_code']);
        }
        if(!empty($data['ac_no'])){
            $sql->where('ps.ac_no',$data['ac_no']);
        }
     if(!empty($data['dist_no'])){
              $sql->where('psl.dist_no',$data['dist_no']);
          }


    $query =  $sql->first();
    if($query){
      if(count($query->total_ps)>0){$total_ps = $query->total_ps;}else{$total_ps = 0;}
      if(count($query->total_exempted)>0){$total_exempted = $query->total_exempted;}else{$total_exempted = 0;}
  
      $data_stats = [
        'total_ps'        => $total_ps,
        'total_exempted'  => $total_exempted,
      ];
    }
    
    return $data_stats;
  
  }

}