<?php namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use DB, Cache;

class PollingStation extends Model
{
  protected $table = 'polling_station as ps';

  protected $connection = 'booth_revamp_test';

  public static function get_polling_stations($filter = array()){

    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->selectRaw('ps.CCODE, ps.ST_CODE, ps.PS_TYPE,ps.AC_NO, ps.PART_NAME, ps.PART_NO, ps.PS_NO, ps.PS_NAME_EN, ps.PS_NAME_V1');

    $sql->where('CONST_TYPE','AC');

    $sql->where("ps.booth_app_excp", 0);
    //$sql->where('ps.ST_CODE',$filter['st_code']);
    //$sql->where('ps.AC_NO',$filter['ac_no']);
    

     if(!empty($filter['phase_no'])){
       $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
     }

     if(!empty($filter['st_code'])){
      $sql->where('ps.ST_CODE',$filter['st_code']);
     }

     if(!empty($filter['ac_no'])){
       $sql->where('ps.AC_NO',$filter['ac_no']);
     }

     if(!empty($filter['ps_no'])){
       $sql->where('ps.PS_NO',$filter['ps_no']);
     }

    $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, CONVERT(ps.PS_NO,INT) ASC");
    $sql->orderByRaw("ps.PS_TYPE DESC");

    return $sql->get()->toArray();

  }
  
  public static function get_polling_stations_exem_new($filter = array()){

    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->join("tbl_analytics_dashboard",[
      ["ps.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["ps.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["ps.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw('ps.CCODE, ps.ST_CODE, ps.AC_NO, ps.PART_NAME, ps.PART_NO, ps.PS_NO, ps.PS_NAME_EN, ps.PS_NAME_V1,tbl_analytics_dashboard.booth_exemp_status,
  tbl_analytics_dashboard.male_electors,tbl_analytics_dashboard.female_electors,
  tbl_analytics_dashboard.other_electors,tbl_analytics_dashboard.male_turnout,
  tbl_analytics_dashboard.female_turnout,tbl_analytics_dashboard.other_turnout,tbl_analytics_dashboard.updated_at');

    $sql->where('CONST_TYPE','AC');
  
    
    $sql->where('tbl_analytics_dashboard.booth_exemp_status',1);

    $sql->where("ps.booth_app_excp", 0);
    
    if(!empty($filter['st_code'])){
      $sql->where('ps.ST_CODE',$filter['st_code']);
     }
	 
	 if(!empty($filter['ac_no'])){
      $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
     }
   

    $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, CONVERT(ps.PS_NO,INT) ASC");

    return $sql->get()->toArray();

  }
  
  public static function get_polling_stations_test($filter = array()){

    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->selectRaw('ps.CCODE, ps.ST_CODE,ps.PS_TYPE, ps.AC_NO, ps.PART_NAME, ps.PART_NO, ps.PS_NO, ps.PS_NAME_EN, ps.PS_NAME_V1');

    $sql->where('CONST_TYPE','AC');
	$sql->where('ps.PS_NO','1');

    $sql->where("ps.booth_app_excp", 0);
    //$sql->where('ps.ST_CODE',$filter['st_code']);
    //$sql->where('ps.AC_NO',$filter['ac_no']);
    

     if(!empty($filter['phase_no'])){
       $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
     }

     if(!empty($filter['st_code'])){
      $sql->where('ps.ST_CODE',$filter['st_code']);
     }

     if(!empty($filter['ac_no'])){
       $sql->where('ps.AC_NO',$filter['ac_no']);
     }

     if(!empty($filter['ps_no'])){
       $sql->where('ps.PS_NO',$filter['ps_no']);
     }

    $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, CONVERT(ps.PS_NO,INT) ASC");
	$sql->orderByRaw("ps.PS_TYPE DESC");

    return $sql->get()->toArray();

  }
  
  
  public static function get_polling_stations_zero_turnout($filter = array()){

    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->join("tbl_analytics_dashboard",[
      ["ps.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["ps.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["ps.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw('ps.CCODE, ps.ST_CODE, ps.AC_NO, ps.PART_NAME, ps.PS_TYPE, ps.PART_NO, ps.PS_NO, ps.PS_NAME_EN, ps.PS_NAME_V1,tbl_analytics_dashboard.booth_exemp_status,
	tbl_analytics_dashboard.male_electors,tbl_analytics_dashboard.female_electors,
	tbl_analytics_dashboard.other_electors,tbl_analytics_dashboard.male_turnout,
	tbl_analytics_dashboard.female_turnout,tbl_analytics_dashboard.other_turnout,tbl_analytics_dashboard.updated_at');

    $sql->where('CONST_TYPE','AC');
	
    
    $sql->where('tbl_analytics_dashboard.booth_exemp_status',1);

    $sql->where("ps.booth_app_excp", 0);
    
    if(!empty($filter['st_code'])){
      $sql->where('tbl_analytics_dashboard.st_code',$filter['st_code']);
     }

     if(!empty($filter['ac_no'])){
       $sql->where('tbl_analytics_dashboard.ac_no',$filter['ac_no']);
     }
   

    $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, CONVERT(ps.PS_NO,INT) ASC");
	  $sql->orderByRaw("ps.PS_TYPE DESC");

    return $sql->get()->toArray();

  }
  
  public static function get_polling_stations_zero_turnout_new($filter = array()){
   
    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->join("tbl_analytics_dashboard",[
      ["ps.ST_CODE","=","tbl_analytics_dashboard.st_code"],
      ["ps.AC_NO","=","tbl_analytics_dashboard.ac_no"],
      ["ps.PS_NO","=","tbl_analytics_dashboard.ps_no"],
    ])->selectRaw('ps.CCODE, ps.ST_CODE, ps.PS_TYPE, ps.AC_NO, ps.PART_NAME, ps.PART_NO, ps.PS_NO, ps.PS_NAME_EN, ps.PS_NAME_V1,tbl_analytics_dashboard.booth_exemp_status,
	tbl_analytics_dashboard.male_electors,tbl_analytics_dashboard.female_electors,
	tbl_analytics_dashboard.other_electors,tbl_analytics_dashboard.male_turnout,
	tbl_analytics_dashboard.female_turnout,tbl_analytics_dashboard.other_turnout,tbl_analytics_dashboard.booth_exemp_status');

    $sql->where('CONST_TYPE','AC');
	if(isset($filter['turnout_type']) && $filter['turnout_type'] == 0){
    $sql->where('tbl_analytics_dashboard.male_turnout',$filter['turnout_type']);
    $sql->where('tbl_analytics_dashboard.female_turnout',$filter['turnout_type']);
    $sql->where('tbl_analytics_dashboard.female_turnout',$filter['turnout_type']);
	}
	$sql->where("ps.booth_app_excp", 0);
    
    if(!empty($filter['st_code'])){
      $sql->where('ps.ST_CODE',$filter['st_code']);
     }

     if(!empty($filter['ac_no'])){
       $sql->where('ps.AC_NO',$filter['ac_no']);
     }
   

    $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, CONVERT(ps.PS_NO,INT) ASC");
    $sql->orderByRaw("ps.PS_TYPE DESC");

    return $sql->get()->toArray();

  }

  public static function get_polling_station($filter = array()){

    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->selectRaw('ps.CCODE, ps.ST_CODE, ps.AC_NO, ps.PART_NAME, ps.PART_NO, ps.PS_NO, ps.PS_NAME_EN, ps.PS_NAME_V1');
	
	 if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }


      if(!empty($filter['st_code'])){
       $sql->where('ps.ST_CODE',$filter['st_code']);
     }

     if(!empty($filter['ac_no'])){
      $sql->where('ps.AC_NO',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps.PS_NO',$filter['ps_no']);
    }

    if(!empty($filter['restricted_ps'])){
      $sql->whereIn('ps.PS_NO',$filter['restricted_ps']);
    }

    $query = $sql->first();
    if(!$query){
      return false;
    }
    return $query->toArray();
    

  }

  public static function total_poll_station_count($filter = array()){
	  
	//$test = DB::connection()->getDatabaseName();
	
    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->selectRaw('ps.id');

    $sql->where('CONST_TYPE','AC');

    $sql->where("ps.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
       $sql->where('ps.ST_CODE',$filter['st_code']);
     }

     if(!empty($filter['ac_no'])){
      $sql->where('ps.AC_NO',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps.PS_NO',$filter['ps_no']);
    }

    return $sql->count();

  }
  
  public static function get_polling_stations_count($filter = array()){
    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->select('ps.ST_CODE','ps.AC_NO',DB::raw('count(ps.ps_no) as total_ps'));

    $sql->where('CONST_TYPE','AC');

    $sql->where("ps.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
     $sql->where('ps.ST_CODE',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ps.AC_NO',$filter['ac_no']);
    }

    $sql->groupBy(['ps.ST_CODE', 'ps.AC_NO']);
    $sql->orderByRaw("ps.ST_CODE, ps.AC_NO ASC");

    return $sql->get()->toArray();

  }

  public static function get_polling_stations_count_ps_wise($filter = array()){
    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])
	->select('ps.ST_CODE','ps.AC_NO','ps.PS_NO','PS_NAME_EN',DB::raw('count(ps.ps_no) as total_ps'));

    $sql->where('CONST_TYPE','AC');

    $sql->where("ps.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
     $sql->where('ps.ST_CODE',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ps.AC_NO',$filter['ac_no']);
    }
	
	if(!empty($filter['ps_no'])){
      $sql->where('ps.ps_no',$filter['ps_no']);
    }

    $sql->groupBy(['ps.ST_CODE', 'ps.AC_NO', 'ps.PS_NO']);
    $sql->orderByRaw("ps.ST_CODE, ps.AC_NO, ps.PS_NO+0 ");

    return $sql->get()->toArray();
  }


}