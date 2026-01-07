<?php 
namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB, Cache;


class ExemPsCountModel extends Model
{
  protected $table = 'polling_station_officer';
  
  
  //protected $connection = 'booth_revamp';
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
    $sql_raw = "COUNT(ps.ps_no) as total_ps,COUNT(IF(psl.is_exempted=1,1,NULL)) as total_exempted,a.AC_NO as ac_no,a.AC_NAME as ac_name";

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
        ])
		->join('boothapp_exempt_polling_station as psl',[
            ['ps.ST_CODE', '=','psl.st_code'],
			['ps.ac_no', '=','psl.ac_no'],
			['ps.ps_no', '=','psl.ps_no']]);

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
  
  public static function total_exempted_ps_pswise($data = array()){
	  
    $election_id = Auth::user()->election_id;
    $sql_raw = "ps.PS_NAME_EN,ps.PS_NO,a.AC_NO as ac_no,a.AC_NAME as ac_name,IFNULL(psl.is_exempted,0) as is_exempted";

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
        ])
		->join('boothapp_exempt_polling_station as psl',[
            ['ps.ST_CODE', '=','psl.st_code'],
			['ps.ac_no', '=','psl.ac_no'],
			['ps.ps_no', '=','psl.ps_no']]);

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

         //$sql->groupBy("a.ac_no");
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
  public static function total_poll_station_count($filter = array()){

  

    $sql = PollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","ps.ST_CODE"],
      ["m_election_details.CONST_NO","=","ps.AC_NO"],
    ])->selectRaw('ps.id');

    $sql->where('CONST_TYPE','AC');

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



}