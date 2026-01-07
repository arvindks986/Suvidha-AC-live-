<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class TblAnalyticsDashboardModel extends Model
{
  protected $table = 'tbl_analytics_dashboard';

  protected $connection = 'booth_revamp_test';
  
  // for connected and disconnected
  
  public static function total_statics_count($filter = array()){
	  
	 
	  
	  $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->select("id", DB::raw('sum(TIMESTAMPDIFF(MINUTE,tbl_analytics_dashboard.updated_at,NOW()) < 15)  AS is_connected'));
	
    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_analytics_dashboard.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['is_started'])){
      $sql->whereNotNull('tbl_analytics_dashboard.poll_started');
    }

     /* if(!empty($filter['is_connected'])){
      $sql->where(DB::raw('TIMESTAMPDIFF(MINUTE,NOW(),updated_at)'), '<', 15);
	  //->whereNull('tbl_analytics_dashboard.poll_ended');
    }  */


    if(!empty($filter['is_end'])){
      $sql->whereNotNull('tbl_analytics_dashboard.poll_ended');
    }


    $sql->where('tbl_analytics_dashboard.status','A');
	
	
    return $sql->count("id");

  }
  
    // for connected and disconnected ends
	
	//new count
	public static function total_statics_count_connected($filter = array()){
	 
		$total = [
        'total_count' => 0,
        'connected_count' => 0
      ];
	  
	  $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->select(DB::raw('COUNT(id) as total_ps'), DB::raw('sum(TIMESTAMPDIFF(MINUTE,tbl_analytics_dashboard.updated_at,NOW()) < 30)  AS is_connected'));
	
    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }


    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }
	$sql->where('tbl_analytics_dashboard.status','A');
	
	$query = $sql->first();
	
	
	
    if($query){
      $total = [
        'total_count' => isset($query->total_ps)?$query->total_ps:0,
        'connected_count' => isset($query->is_connected)?$query->is_connected:0
      ];
    }
	
    return $total;

  }
	
	//new count ends
	
	
	// for incident count
	public static function get_incident_count($filter = array()){

    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->select("tbl_analytics_dashboard.incident");
    
    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }


    $sql->where('tbl_analytics_dashboard.status','A');

    return $sql->sum("tbl_analytics_dashboard.incident");

  }
	
	//for incident count ends
	
	//material received or not
	public static function total_material_count($filter = array()){

    $total = [
      'total_submited' => 0,
      'total_received' => 0
    ];

    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw("COUNT(IF(pm_submitted,1,NULL)) AS total_submited, COUNT(IF(pm_received,1,NULL)) AS total_received");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

    $query = $sql->where('tbl_analytics_dashboard.status','A')->first();
    if($query){
      $total = [
        'total_submited' => $query->total_submited,
        'total_received' => $query->total_received
      ];
    }

    return $total;

  }
	
	//material received or not ends
  
  
  // pwd voters 

  public static function get_pwd_voters_electors($filter = array()){
    $data_stats = [
      'pwd_e_male_new'  => 0,
      'pwd_e_female_new'  => 0,
      'pwd_e_other_new'  => 0,
      'pwd_v_male_new'  => 0,
      'pwd_v_female_new'  => 0,
      'pwd_v_other_new'  => 0,

    ];

    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw("IFNULL(SUM(pwd_male_electors),0) as pwd_e_male_new, IFNULL(SUM(pwd_female_electors),0) as pwd_e_female_new, IFNULL(SUM(pwd_other_electors),0) as pwd_e_other_new, IFNULL(SUM(pwd_male_voters),0) as pwd_v_male_new, IFNULL(SUM(pwd_female_voters),0) as pwd_v_female_new, IFNULL(SUM(pwd_other_voters),0) as pwd_v_other_new");


     

     if(!empty($filter['phase_no'])){
       $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
     }

     if(!empty($filter['st_code'])){
       $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
     }



     if(!empty($filter['ac_no'])){
       $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
     }

     if(!empty($filter['ps_no'])){
       $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
     }

    $query = $sql->first();

    //dd($query);

    if($query){
      $data_stats = [
        'pwd_e_male_new'  => $query->pwd_e_male_new,
        'pwd_e_female_new'  => $query->pwd_e_female_new,
        'pwd_e_other_new'  => $query->pwd_e_other_new,
        'pwd_v_male_new'  => $query->pwd_v_male_new,
        'pwd_v_female_new'  => $query->pwd_v_female_new,
        'pwd_v_other_new'  => $query->pwd_v_other_new,

      ];
    }

    return $data_stats;
  }

  // pwd voters ends
  
  //sum total stats
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
      'form_49_count'   => 0,
      'tendered_count'=>0,
    ];

    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
      ])->selectRaw("IFNULL(SUM(pro_turnout),0) as pro_turn_out, (IFNULL(SUM(blo_turnout),0) - IFNULL(SUM(pro_turnout),0)) as queue_voters, IFNULL(SUM(male_turnout),0) as male_voters, IFNULL(SUM(female_turnout),0) as female_voters, IFNULL(SUM(other_turnout),0) as other_voters, IFNULL(SUM(scan_qr),0) as scan_qr, IFNULL(SUM(scan_srno),0) as scan_srno, IFNULL(SUM(scan_epicno),0) as scan_epicno,IFNULL(SUM(form_49_count),0) as form_49_count,IFNULL(SUM(tendered_count),0) as tendered_count, IFNULL(SUM(scan_name),0) as scan_name, IFNULL(SUM(scan_mobile),0) as scan_mobile,tbl_analytics_dashboard.updated_at as last_sync_time");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_analytics_dashboard.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

    $sql->where('tbl_analytics_dashboard.status','A');

    $query =  $sql->first();
    if($query){
      $data_stats = [
        'pro_turn_out'  => $query->pro_turn_out,
        'queue_voters'  => $query->queue_voters,
        'male_voters'   => $query->male_voters,
        'female_voters' => $query->female_voters,
        'other_voters'  => $query->other_voters,
        'scan_qr'       => $query->scan_qr,
        'scan_srno'     => $query->scan_srno,
        'scan_epicno'   => $query->scan_epicno,
        'scan_name'     => $query->scan_name,
        'scan_mobile'   => $query->scan_mobile,
        'last_sync_time'   => $query->last_sync_time,
        'form_49_count'   => $query->form_49_count,
        'tendered_count'=>$query->tendered_count,
      ];
    }

    return $data_stats;
  }
  
  //gender data
  public static function get_voter_count($filter = array()){

    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw("IFNULL(SUM(male_turnout),0) as male, IFNULL(SUM(female_turnout),0) as female,
                  IFNULL(SUM(other_turnout),0) as other");

    $sql->where("polling_station.booth_app_excp", 0);

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

   

    $sql->where('tbl_analytics_dashboard.status','A');

	$query =  $sql->first();
    if(!$query){
      return false;
    }
    return $query->toArray();

    

  }
  
  //gender data ends




  public static function get_aggregate_voters($filter = array()){

    $data = [
      'e_male' => 0,
      'e_female' => 0,
      'e_other' => 0,
      'e_total' => 0,
    ];
    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw("IFNULL(SUM(male_electors),0) as e_male_new, IFNULL(SUM(female_electors),0) as e_female_new, IFNULL(SUM(other_electors),0) as e_other_new");
	

    $sql->where('CONST_TYPE','AC');
    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
       $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
     }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

    $result = $sql->first();
    
    if($result){
      $data = [
        'e_male' => $result->e_male_new,
        'e_female' => $result->e_female_new,
        'e_other' => $result->e_other_new,
        'e_total' => $result->e_male_new+$result->e_female_new+$result->e_other_new,
      ];
    }

    return $data;
  
  }
  
  
  public static function get_age_group($filter = array()){
    $data = [];
    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw("IFNULL(SUM(age_18_25),0) as age_18_25, IFNULL(SUM(age_26_30),0) as age_26_30,
                  IFNULL(SUM(age_31_40),0) as age_31_40, IFNULL(SUM(age_41_50),0) as age_41_50,
                  IFNULL(SUM(age_51_60),0) as age_51_60, IFNULL(SUM(age_61_70),0) as age_61_70,
                  IFNULL(SUM(age_71_80),0) as age_71_80,IFNULL(SUM(age_81_90),0) as age_81_90,
                  IFNULL(SUM(age_91_100),0) as age_91_100,
                  IFNULL(SUM(age_100_above),0) as age_100_above");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
       $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
     }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }


    $sql->where('tbl_analytics_dashboard.status','A');

    $result = $sql->first();

    if($result){
      $data = array_values($result->toArray());
    }

    return $data;
  }
  
  
  public static function half_hour_times($filter = array()) {
    $start_time = '07:00';
    $end_time   = date('H:i');
    $phase_date = PhaseModel::get_phase_date($filter);
    if( date('Y-m-d') <= $phase_date && date('H:i')< '18:00'){
      $end_time   = date('H:i');
	  //$end_time   = '18:00';
    }else{
      $end_time   = '18:00';
    }
  
    if(strtotime($end_time) > strtotime(date('H:i'))){
      $end_time = '18:00';
    }
	
   
    $time_slot_label_for_line = [];
    $time_slot_label_for_line[] = $start_time;
    while(strtotime($start_time) < strtotime($end_time)){
      $start_time = date('H:i', strtotime("30 minutes", strtotime($start_time)));
      $time_slot_label_for_line[] = $start_time;
    }
	
    //for removing last index of time slot , as per requirement.
    if(count($time_slot_label_for_line)>1)
    {
      unset($time_slot_label_for_line[count($time_slot_label_for_line)-1]);
      //unset($time_slot_label_for_line[count($time_slot_label_for_line)-1]);
    }
    return $time_slot_label_for_line;

  }
  
  public static function get_voters_by_time($filter = array()){
    $data = [];
    $sub_sql_array = [];
    if(!empty($filter['is_cumulative'])){
      $i = 0;
      $date = PhaseModel::get_phase_date($filter);
		
      foreach($filter['is_cumulative'] as $itr_cum){
		 
        $fix_in_time  = $date.' '.date('H:i:s', strtotime($itr_cum));
		
        $fix_out_time = $date.' '. date('H:i:s', strtotime("+30 minutes", strtotime($itr_cum)));
		if($i > 0){
			$fix_in_time_new = (int)(str_replace(':','',date('H:i', strtotime("+1 minutes",strtotime($itr_cum)))));
			$fix_out_time_new = (int)(str_replace(':','',date('H:i', strtotime("+30 minutes", strtotime($itr_cum)))));
		}else{
			$fix_in_time_new = (int)(str_replace(':','',date('H:i', strtotime($itr_cum))));
			$fix_out_time_new = (int)(str_replace(':','',date('H:i', strtotime("+30 minutes", strtotime($itr_cum)))));
		}
		if($fix_in_time_new<= 1800){
			$sub_sql_array[] = "IFNULL(SUM(time_".$fix_in_time_new."_".$fix_out_time_new."),0) as time".$i;
		}
		
		
        $data[$i] = 0;
        $i++;
		}
    }

    if(!empty($filter['is_time_slap'])){
      $i = 0;
      $date = PhaseModel::get_phase_date($filter);

      foreach($filter['is_time_slap'] as $itr_cum){
        $fix_in_time  = $date.' '.date('H:i:s', strtotime($itr_cum));
        $fix_out_time = $date.' '. date('H:i:s', strtotime("+120 minutes", strtotime($itr_cum)));
        $sub_sql_array[] = "COUNT(IF(in_out_time > '".$fix_in_time."' AND in_out_time <= '".$fix_out_time."',1,NULL)) as timedata".$i;
        $data[$i] = 0;
        $i++;
      }
    }

    $sub_sql = implode(',',$sub_sql_array);
	
	


    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw($sub_sql);

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }


    $sql->where('tbl_analytics_dashboard.status','A');

    $result = $sql->first();

    if($result){
      $data = array_values($result->toArray());
    }

    return $data;
	
	
  }
  
  
  public static function get_cumulative_time_data($filter = array()){
    $data = [];
    $sub_sql_array = [];
    if(!empty($filter['is_cumulative'])){
      $i = 0;
      $date = PhaseModel::get_phase_date($filter);

      foreach($filter['is_cumulative'] as $itr_cum){
        $in_time  = $date.' '.date('H:i:s', strtotime($itr_cum));
		if($i < 22){
		if($i > 0){
			$in_time_new = (int)(str_replace(':','',date('H:i', strtotime("+1 minutes",strtotime($itr_cum)))));
			$out_time_new = (int)(str_replace(':','',date('H:i', strtotime("+30 minutes", strtotime($itr_cum)))));
		}else{
			$in_time_new = (int)(str_replace(':','',date('H:i', strtotime($itr_cum))));
			$out_time_new = (int)(str_replace(':','',date('H:i', strtotime("+30 minutes", strtotime($itr_cum)))));
		}
		}
        $sub_sql_array[] = "IFNULL(SUM(time_".$in_time_new."_".$out_time_new."),0) as time".$i;
        $data[$i] = 0;
        $i++;
      }
    }

    $sub_sql = implode(',',$sub_sql_array);



    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw($sub_sql);

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }


    $sql->where('tbl_analytics_dashboard.status','A');

    $result = $sql->first();

    if($result){
      $data = array_values($result->toArray());
    }

    return $data;
  }
  
  
  //sum total stats
  public static function get_poll_summary($filter = array()){

    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->where('CONST_TYPE','AC')->selectRaw("tbl_analytics_dashboard.id, tbl_analytics_dashboard.st_code,  tbl_analytics_dashboard.ac_no, tbl_analytics_dashboard.ps_no, poll_started, electors, pro_turnout, blo_turnout, total_turnout, male_turnout, female_turnout, other_turnout, scan_qr, scan_srno, scan_epicno, scan_name, scan_mobile, poll_ended");

    $sql->where("polling_station.booth_app_excp", 0);


    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_analytics_dashboard.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

    $sql->where('tbl_analytics_dashboard.status','A');

    $query =  $sql->first();
    if(!$query){
      return false;
    }
    return $query->toArray();
  }
  
  
    //BLO and PRO turnout
  //sum total stats
  public static function total_blo_pro_turnout_statics($filter = array()){
    $data_stats = [
      'pro_turn_out'  => 0,
      'blo_turn_out'  => 0
    ];

    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw("IFNULL(SUM(pro_turnout),0) as pro_turn_out, (IFNULL(SUM(blo_turnout),0)) as blo_turn_out");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }


    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

    $sql->where('tbl_analytics_dashboard.status','A');

    $query =  $sql->first();
    if($query){
      $data_stats = [
        'pro_turn_out'  => $query->pro_turn_out,
        'blo_turn_out'  => $query->blo_turn_out
      ];
    }


    return $data_stats;
  }
  
  //BLO and PRO turnout
  //sum total stats
  public static function total_blo_pro_zero($filter = array()){

    $sql = TblAnalyticsDashboardModel::join('polling_station',[
      ["polling_station.st_code","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.ac_no","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.ps_no","=","tbl_analytics_dashboard.ps_no"],
    ])->join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->selectRaw("polling_station.ST_CODE,polling_station.AC_NO,polling_station.PS_NO,polling_station.PS_NAME_EN,IFNULL(SUM(pro_turnout),0) as pro_turn_out, (IFNULL(SUM(blo_turnout),0)) as blo_turn_out");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

  

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

	if(!empty($filter['is_vote'])){
		if($filter['is_vote'] == '1'){
			$sql->where('tbl_analytics_dashboard.blo_turnout','0');
		}else if($filter['is_vote'] == '2'){
			$sql->where('tbl_analytics_dashboard.pro_turnout','0');
		}else if($filter['is_vote'] == '3'){
			$sql->where([['tbl_analytics_dashboard.blo_turnout','0'],['tbl_analytics_dashboard.pro_turnout','0']]);
		}
    }


    $sql->where('tbl_analytics_dashboard.status','A');
    $sql->groupBy('tbl_analytics_dashboard.st_code', 'tbl_analytics_dashboard.ac_no','tbl_analytics_dashboard.ps_no');
	$sql->orderByRaw('tbl_analytics_dashboard.st_code', 'tbl_analytics_dashboard.ac_no','tbl_analytics_dashboard.ps_no');

    $query =  $sql->get()->toArray();



    return $query;
  }
  
  public static function get_analytics_data($filter = array()){
	  
	$sql = TblAnalyticsDashboardModel::select();
	
	if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }
	  
	if(!empty($filter['ac_no'])){	
      $sql->where('ac_no',$filter['ac_no']);
    }
	
	if(!empty($filter['ps_no'])){	
      $sql->where('ps_no',$filter['ps_no']);
    }
	
	$sql->orderBy(DB::raw('ac_no+0'),'ASC');
	$sql->orderBy(DB::raw('ps_no+0'),'ASC');
	
	$query =  $sql->get()->toArray();
	return $query;
  }
  
  public static function total_exempted_ps($data = array()){
	  
     
      //STATE FUNCTION STARTS
      $sql_raw = "COUNT(tbl_analytics_dashboard.ps_no) as total_ps,COUNT(IF(tbl_analytics_dashboard.booth_exemp_status=1,1,NULL)) as total_exempted";

	   
        $sql = TblAnalyticsDashboardModel::selectRaw($sql_raw);
  
	
        //$sql->selectRaw($sql_raw);

       
      
        if(!empty($data['st_code'])){
          $sql->where("tbl_analytics_dashboard.st_code", $data['st_code']);
        }
        if(!empty($data['ac_no'])){
            $sql->where('tbl_analytics_dashboard.ac_no',$data['ac_no']);
        }
		

        $query = $sql->get();

        
        return $query;
  
  }
  
  public static function total_exempted_ps_ac($data = array()){
    
    $sql_raw = "COUNT(tbl_analytics_dashboard.ps_no) as total_ps,COUNT(IF(tbl_analytics_dashboard.booth_exemp_status=1,1,NULL)) as total_exempted,a.AC_NO as ac_no,a.AC_NAME_EN as ac_name,tbl_analytics_dashboard.st_code";

	   
		
		$sql = TblAnalyticsDashboardModel::join('ac_master as a',[ 
              ['a.ac_no', '=','tbl_analytics_dashboard.ac_no'],
              ['a.st_code', '=','tbl_analytics_dashboard.st_code'],
        ]);
		
		
        

        $sql->selectRaw($sql_raw);

       
        
         if(!empty($data['st_code'])){
            $sql->where("tbl_analytics_dashboard.st_code", $data['st_code']);
          }

          if(!empty($data['dist_no'])){
              $sql->where('a.DIST_NO_HDQTR',$data['dist_no']);
          }

          if(!empty($data['ac_no'])){
              $sql->where('tbl_analytics_dashboard.ac_no',$data['ac_no']);
          }

         $sql->groupBy("a.AC_NO");
         $sql->orderByRaw("tbl_analytics_dashboard.st_code, a.AC_NO, a.AC_NAME_EN ASC");
		 

        $query = $sql->get();
		
		
        return $query;
  }


public static function total_exempted_ps_pswise($data = array()){
	  
    
    $sql_raw = "ps.PS_NAME_EN,ps.PS_NO,ps.PS_TYPE,ps.AC_NO,a.AC_NO as ac_no,a.AC_NAME_EN as ac_name,IFNULL(tbl_analytics_dashboard.booth_exemp_status,1) as is_exempted,tbl_analytics_dashboard.st_code";

	   
		
		$sql = TblAnalyticsDashboardModel::join('ac_master as a',[ 
              ['a.ac_no', '=','tbl_analytics_dashboard.ac_no'],
              ['a.st_code', '=','tbl_analytics_dashboard.st_code'],
        ])->join('polling_station as ps',[
              ['tbl_analytics_dashboard.ac_no', '=','ps.ac_no'],
              ['tbl_analytics_dashboard.st_code', '=','ps.st_code'],
              ['tbl_analytics_dashboard.ps_no', '=','ps.PS_NO'],
        ]);
		

        $sql->selectRaw($sql_raw);

        
        
         if(!empty($data['st_code'])){
            $sql->where("tbl_analytics_dashboard.st_code", $data['st_code']);
          }

          if(!empty($data['dist_no'])){
              $sql->where('a.DIST_NO_HDQTR',$data['dist_no']);
          }

          if(!empty($data['ac_no'])){
              $sql->where('tbl_analytics_dashboard.ac_no',$data['ac_no']);
          }

         //$sql->groupBy("a.ac_no");
         //$sql->orderByRaw("tbl_analytics_dashboard.st_code, a.AC_NO, ps.PS_NO ASC");
		 $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, CONVERT(ps.PS_NO,INT) ASC");
         $sql->orderByRaw("ps.PS_TYPE DESC");
   

        $query = $sql->get();
		
		
        return $query;
  }
  
  
  public static function total_statics_sum_exem_new($filter = array()){
   
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

    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw("IFNULL(SUM(pro_turnout),0) as pro_turn_out, (IFNULL(SUM(blo_turnout),0) - IFNULL(SUM(pro_turnout),0)) as queue_voters, IFNULL(SUM(male_turnout),0) as male_voters, IFNULL(SUM(female_turnout),0) as female_voters, IFNULL(SUM(other_turnout),0) as other_voters, IFNULL(SUM(scan_qr),0) as scan_qr, IFNULL(SUM(scan_srno),0) as scan_srno, IFNULL(SUM(scan_epicno),0) as scan_epicno, IFNULL(SUM(scan_name),0) as scan_name, IFNULL(SUM(scan_mobile),0) as scan_mobile,tbl_analytics_dashboard.updated_at as last_sync");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.phase_no',$filter['phase_no']);
    }
	
	
      $sql->where('tbl_analytics_dashboard.booth_exemp_status',1);
    

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_analytics_dashboard.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

    $sql->where('tbl_analytics_dashboard.status','A');

    $query =  $sql->first();
	
    if($query){
      $data_stats = [
        'pro_turn_out'  => $query->pro_turn_out,
        'queue_voters'  => $query->queue_voters,
        'male_voters'   => $query->male_voters,
        'female_voters' => $query->female_voters,
        'other_voters'  => $query->other_voters,
        'scan_qr'       => $query->scan_qr,
        'scan_srno'     => $query->scan_srno,
        'scan_epicno'   => $query->scan_epicno,
        'scan_name'     => $query->scan_name,
        'scan_mobile'   => $query->scan_mobile,
        'last_sync'   => $query->last_sync,
      ];
    }

    return $data_stats;
	
	
  }
  
  public static function get_aggregate_voters_exem_new($filter = array()){

    $data = [
      'e_male' => 0,
      'e_female' => 0,
      'e_other' => 0,
      'e_total' => 0,
    ];
    $sql = TblAnalyticsDashboardModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_analytics_dashboard.st_code"],
      ["m_election_details.CONST_NO","=","tbl_analytics_dashboard.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw("IFNULL(SUM(male_electors),0) as e_male_new, IFNULL(SUM(female_electors),0) as e_female_new, IFNULL(SUM(other_electors),0) as e_other_new");

    $sql->where('CONST_TYPE','AC');
    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
       $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
     }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
    }
	
	
      $sql->where('tbl_analytics_dashboard.booth_exemp_status',1);
    

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_analytics_dashboard.ps_no',$filter['ps_no']);
    }

    $result = $sql->first();
    
    if($result){
      $data = [
        'e_male' => $result->e_male_new,
        'e_female' => $result->e_female_new,
        'e_other' => $result->e_other_new,
        'e_total' => $result->e_male_new+$result->e_female_new+$result->e_other_new,
      ];
    }

    return $data;
  
  }



}
