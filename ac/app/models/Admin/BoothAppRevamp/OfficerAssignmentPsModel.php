<?php 
namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB, Cache;


class OfficerAssignmentPsModel extends Model
{
  protected $table = 'polling_station_officer';

  //protected $connection = 'booth_revamp';
  
  //PO PRO COUNT
  public static function ps_po_pro_ac($data = array()){

    $election_id = Auth::user()->election_id;
    $sql_raw = "a.AC_NO as ac_no,a.AC_NAME as ac_name,COUNT(DISTINCT(IF(role_id=34,p.ps_no,NULL))) total_po,COUNT(DISTINCT(IF(role_id=35,p.ps_no,NULL))) total_pro,ps.PS_NO as ps_no,ps.PS_NAME_EN as ps_name";

    $sql = DB::table('boothapp_enable_acs as b')
    ->join('m_election_details as e',[
      ['b.st_code', '=','e.st_code'],
      ['b.ac_no', '=','e.CONST_NO'],
    ])

    ->join('m_ac as a',[ 
      ['a.ac_no', '=','b.ac_no'],
      ['a.st_code', '=','b.st_code'],
    ])
    ->leftjoin('polling_station_officer as p',[
      ['b.ac_no', '=','p.ac_no'],
      ['b.st_code', '=','p.st_code'],
    ])
    ->join('polling_station as ps',[
      ['p.st_code', '=','ps.ST_CODE'],
      ['p.ac_no', '=','ps.AC_NO'],
    ])

    ->leftjoin('polling_station_location as psl',[
      ['p.location_id', '=','psl.id']
    ])

    ->leftjoin('polling_station_location_to_ps as pslp',[
      ['psl.id', '=','pslp.location_id']
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

    if(!empty($data['ps_no'])){
      $sql->where('ps.ps_no',$data['ps_no']);
    }

    $sql->groupBy("ps.PS_NO")->groupBy("p.id");
    $sql->orderByRaw("ps.PS_NO ASC");


    $query = $sql->first();

        //CONVERTING OBJECT TO ARRAY WITH COLLECTION STARTS
        /*$array = $query->map(function($obj){
          return (array) $obj;
        })->toArray();*/
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION ENDS

        $query = $sql->first();

        if(!$query){
          return $query =[];
        }
        return $query;

      }


    //BLO COUNT
      public static function ps_blo_ac($data = array()){

        $election_id = Auth::user()->election_id;

        $sql_raw = "COUNT(DISTINCT(IF(role_id=33,p.ps_no,NULL))) total_blo,ps.PS_NO as ps_no,ps.PS_NAME_EN as ps_name";
        $sql = DB::table('polling_station_officer as p')
        ->join('boothapp_enable_acs as b',[
          ['b.ac_no', '=','p.ac_no'],
          ['b.st_code', '=','p.st_code'],
        ])
        ->join('polling_station as ps',[
          ['p.st_code', '=','ps.st_code'],
          ['p.ac_no', '=','ps.ac_no'],
        ])
        ->join('m_election_details as e',[
          ['p.st_code', '=','e.st_code'],
          ['p.ac_no', '=','e.CONST_NO'],
        ])
        ->join('m_ac as a',[
          ['a.ac_no', '=','p.ac_no'],
          ['a.st_code', '=','p.st_code'],
        ])
        ->join('polling_station_location as psl',[
          ['p.location_id', '=','psl.id'],
        ])
        ->join('polling_station_location_to_ps as pslp',[
          ['psl.id', '=','pslp.location_id'],
        ]);


        $sql->selectRaw($sql_raw);

        $sql->where('e.CONST_TYPE', '=', 'AC');
        $sql->where('e.election_status', '=', '1');
        $sql->where('e.ELECTION_ID',$election_id);
        
        if(!empty($data['st_code'])){
          $sql->where("p.st_code", $data['st_code']);
        }

        if(!empty($data['dist_no'])){
          $sql->where('a.DIST_NO_HDQTR',$data['dist_no']);
        }

        if(!empty($data['ac_no'])){
          $sql->where('p.ac_no',$data['ac_no']);
        }

        if(!empty($data['ps_no'])){
          $sql->where('ps.ps_no',$data['ps_no']);
        }

        $sql->groupBy("ps.PS_NO")->groupBy("p.id");
        $sql->orderByRaw("ps.PS_NO ASC");

        $query = $sql->first();

        if(!$query){
          return $query =[];
        }
        return $query;

      }



    //SM COUNT
      public static function ps_sm_ac($data = array()){
        $election_id = Auth::user()->election_id;

        $sql_raw = "COUNT(DISTINCT(ps_officer_id)) total_sm,ps.PS_NO as ps_no,ps.PS_NAME_EN as ps_name";
        $sql = DB::table('polling_station_officer as p')
        ->leftjoin('ps_sector_officer as pso',[
          ['p.id', '=','pso.ps_officer_id'],
          ['p.st_code', '=','pso.st_code'],
        ])
        ->join('polling_station as ps',[
          ['p.st_code', '=','ps.st_code'],
          ['p.ac_no', '=','ps.ac_no'],
        ])
        ->join('m_ac as a',[
          ['a.ac_no', '=','p.ac_no'],
          ['a.st_code', '=','p.st_code'],
        ]);


        $sql->selectRaw($sql_raw);

        $sql->where("p.role_id", '38');
        $sql->where("pso.is_deleted", '0');
        
        if(!empty($data['st_code'])){
          $sql->where("p.st_code", $data['st_code']);
        }

        if(!empty($data['dist_no'])){
          $sql->where('a.DIST_NO_HDQTR',$data['dist_no']);
        }

        if(!empty($data['ac_no'])){
          $sql->where('p.ac_no',$data['ac_no']);
        }

        if(!empty($data['ps_no'])){
          $sql->where('ps.ps_no',$data['ps_no']);
        }

        $sql->groupBy("ps.PS_NO")->groupBy("p.id");
        $sql->orderByRaw("ps.PS_NO ASC");


        $query = $sql->first();

        //CONVERTING OBJECT TO ARRAY WITH COLLECTION STARTS
        /*$array = $query->map(function($obj){
          return (array) $obj;
        })->toArray();*/
        //CONVERTING OBJECT TO ARRAY WITH COLLECTION ENDS

        if(!$query){
          return $query = []; 
        }
        return $query; 

      }

     //get_officer
      public static function get_officer($data = array()){

        $election_id = Auth::user()->election_id;
        $sql_raw = "p.id, p.name, p.mobile_number, p.email, p.designation, p.role_id, p.st_code, p.district_no, p.ac_no, p.ps_no, p.is_active, p.role_level";

        $sql = DB::table('polling_station_officer as p')
        ->leftjoin('boothapp_enable_acs as b',[
          ['b.ac_no', '=','p.ac_no'],
          ['b.st_code', '=','p.st_code'],
        ])
        ->join('m_election_details as e',[
          ['p.st_code', '=','e.st_code'],
          ['p.ac_no', '=','e.CONST_NO'],
        ])
        ->join('m_ac as a',[ 
          ['a.ac_no', '=','p.ac_no'],
          ['a.st_code', '=','p.st_code'],
        ])
        ->leftjoin('polling_station_location_to_ps as pslp',[
          ['pslp.id', '=','p.location_id']
        ]);


        $sql->selectRaw($sql_raw);
        
        $sql->where('e.CONST_TYPE', '=', 'AC');
        $sql->where('e.election_status', '=', '1');
        $sql->where('e.ELECTION_ID',$election_id);
        

        if(!empty($data['st_code'])){
          $sql->where("b.st_code", $data['st_code']);
        }



        if(!empty($data['dist_no'])){
          $sql->where('b.dist_no',$data['dist_no']);
        }

       if(!empty($data['ac_no'])){
          $sql->where('a.ac_no',$data['ac_no']);
        }
       if(!empty($data['ps_no'])){
          $sql->where('pslp.ps_no',$data['ps_no']);
        }
      
       if(!empty($data['id'])){
          $sql->where('id',$data['id']);
        }

        if(!empty($data['role_id'])){
          $sql->where('role_id',$data['role_id']);
        }

        if(!empty($data['mobile'])){
          $sql->where('mobile_number',$data['mobile']);
        }

        $query =  $sql->first();

        $data_stats =[];
        if($query) {
          $data_stats = [
            'id'            => $query->id,
            'name'          => $query->name,
            'mobile_number' => $query->mobile_number,
            'email'         => $query->email,
            'designation'   => $query->designation,
            'role_id'       => $query->role_id,
            'st_code'       => $query->st_code,
            'district_no'   => $query->district_no,
            'ac_no'         => $query->ac_no,
            'ps_no'         => $query->ps_no,
            'is_active'     => $query->is_active,
            'role_level'    => $query->role_level,
            
          ];
        }else{
          $data_stats = [
            'id'            => '',
            'name'          => '',
            'mobile_number' => '',
            'email'         => '',
            'designation'   => '',
            'role_id'       => '',
            'st_code'       => '',
            'district_no'   => '',
            'ac_no'         => '',
            'ps_no'         => '',
            'is_active'     => '',
            'role_level'    => '',
            
          ];
        }
    
    return $data_stats;
         

      }



    }