<?php 
namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB, Cache;


class ClearPsCountModel extends Model
{
  protected $table = 'polling_station_officer';
  
  
  //protected $connection = 'booth_revamp';
  
  public static function clear_poll_data($data = array()){
	  $election_id = Auth::user()->election_id;
	  $sql_raw = "id ,reset_datetime";
	  $sql = DB::connection('booth_revamp')->table('tbl_booth_user as bu')
        ->join('m_election_details as e',[
              ['bu.st_code', '=','e.st_code'],
              ['bu.ac_no', '=','e.CONST_NO'],
        ]);
	$sql->selectRaw($sql_raw);
	$sql->where('e.CONST_TYPE', '=', 'AC');
	$sql->where('e.election_status', '=', '1');
	$sql->where('e.ELECTION_ID',$election_id);
  
	if(!empty($data['st_code'])){
	  $sql->where("bu.st_code", $data['st_code']);
	}
	if(!empty($data['ac_no'])){
		$sql->where('bu.ac_no',$data['ac_no']);
	}
	$sql->where('bu.reset_flag','1');
	$sql->groupBy("bu.userid");
	$query = $sql->get();
	//dd($query);
	return $query;
  }
  public static function total_exempted_ps($data = array()){
     $election_id = Auth::user()->election_id;

      //STATE FUNCTION STARTS
      $sql_raw = "COUNT(ps.ps_no) as total_ps";

	   $sql = DB::table('boothapp_enable_acs as b')
        ->join('m_election_details as e',[
              ['b.st_code', '=','e.st_code'],
              ['b.ac_no', '=','e.CONST_NO'],
        ])
        ->leftjoin('boothapp_polling_station as ps',[
              ['b.ac_no', '=','ps.ac_no'],
              ['b.st_code', '=','ps.st_code'],
        ]);
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

        $query = $sql->get();

        //CONVERTING OBJECT TO ARRAY WITH COLLECTION STARTS
        $array = $query->map(function($obj){
          return (array) $obj;
        })->toArray();
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION ENDS

        if(!$array){
            return [];
        }
        return $array;
  
  }

 public static function total_exempted_ps_ac($data = array()){
    $election_id = Auth::user()->election_id;
    $sql_raw = "COUNT(ps.ps_no) as total_ps,a.AC_NO as ac_no,a.AC_NAME as ac_name";

	   $sql = DB::table('boothapp_enable_acs as b')
        ->join('m_election_details as e',[
              ['b.st_code', '=','e.st_code'],
              ['b.ac_no', '=','e.CONST_NO'],
        ])
		->join('m_ac as a',[ 
              ['a.ac_no', '=','b.ac_no'],
              ['a.st_code', '=','b.st_code'],
        ])
        ->join('boothapp_polling_station as ps',[
              ['b.ac_no', '=','ps.ac_no'],
              ['b.st_code', '=','ps.st_code'],
        ]);

        $sql->selectRaw($sql_raw);

        $sql->where('e.CONST_TYPE', '=', 'AC');
        $sql->where('e.election_status', '=', '1');
        $sql->where('e.ELECTION_ID',$election_id);
        
         if(!empty($data['st_code'])){
            $sql->where("b.st_code", $data['st_code']);
          }

          if(!empty($data['dist_no'])){
              $sql->where('a.DIST_NO_HDQTR',$data['dist_no']);
          }

          if(!empty($data['ac_no'])){
              $sql->where('a.ac_no',$data['ac_no']);
          }


         $sql->groupBy("a.ac_no");
         $sql->orderByRaw("b.st_code, a.ac_no, a.ac_name ASC");
   

        $query = $sql->get();
		
		//dd($query);

        //CONVERTING OBJECT TO ARRAY WITH COLLECTION STARTS
        $array = $query->map(function($obj){
          return (array) $obj;
        })->toArray();
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION ENDS

        if(!$array){
            return [];
        }
        return $array;
  }
  
  public static function total_cleared_ps_pswise($data = array()){
	  
    $election_id = Auth::user()->election_id;
    $sql_raw = "ps.PS_NAME_EN,ps.PS_NO,a.AC_NO as ac_no,a.AC_NAME_EN as ac_name,IFNULL(bu.reset_flag,0) as reset_flag,bu.userid,bu.reset_datetime as reset_date,(CASE WHEN  user_type=33 THEN 'BLO' WHEN  user_type=34 THEN 'PO' WHEN  user_type=35 THEN 'PRO' ELSE 'SM' end) as usertype";

	   $sql = DB::connection('booth_revamp')->table('tbl_booth_user as bu')
        ->join('m_election_details as e',[
              ['bu.st_code', '=','e.st_code'],
              ['bu.ac_no', '=','e.CONST_NO'],
        ])
		->leftjoin('ac_master as a',[ 
              ['a.ac_no', '=','bu.ac_no'],
              ['a.st_code', '=','bu.st_code'],
        ])
        ->leftjoin('polling_station as ps',[
              ['bu.ac_no', '=','ps.ac_no'],
              ['bu.st_code', '=','ps.st_code'],
        ]);

        $sql->selectRaw($sql_raw);

        $sql->where('e.CONST_TYPE', '=', 'AC');
        $sql->where('e.election_status', '=', '1');
        $sql->where('e.ELECTION_ID',$election_id);
        
         if(!empty($data['st_code'])){
            $sql->where("bu.st_code", $data['st_code']);
          }

          if(!empty($data['dist_no'])){
              $sql->where('a.DIST_NO_HDQTR',$data['dist_no']);
          }

          if(!empty($data['ac_no'])){
              $sql->where('a.ac_no',$data['ac_no']);
          }
		 $sql->where('bu.reset_flag','1');
         $sql->groupBy("bu.userid");
         $sql->orderByRaw("ps.PS_NO+0,ps.PS_NAME_EN ASC");
   

        $query = $sql->get();
		
		//dd($query);

        //CONVERTING OBJECT TO ARRAY WITH COLLECTION STARTS
        $array = $query->map(function($obj){
          return (array) $obj;
        })->toArray();
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION ENDS

        if(!$array){
            return [];
        }
        return $array;
  }
  
  public static function get_officer_name_by_id($data = array()){
	 $user_data = [
      'name' => '',
      'mobile' => '',
    ];
    $election_id = Auth::user()->election_id;
    $sql_raw = "name,mobile_number";

	   $sql = DB::table('polling_station_officer');
        $sql->selectRaw($sql_raw);
        //$sql->where('election_id',$election_id);
        
        if(!empty($data['st_code'])){
            $sql->where("st_code", $data['st_code']);
        }

        if(!empty($data['dist_no'])){
            $sql->where('district_no',$data['dist_no']);
        }

        if(!empty($data['ac_no'])){
              $sql->where('ac_no',$data['ac_no']);
        }
		if(!empty($data['userid'])){
              $sql->where('id',$data['userid']);
        }
		
		$query = $sql->first();
		
        if($query){
		  $user_data = [
			'name' => $query->name,
			'mobile' => $query->mobile_number,
		  ];
        }
    return $user_data;
  }



}