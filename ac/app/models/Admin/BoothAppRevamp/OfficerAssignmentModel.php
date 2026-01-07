<?php 
namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB, Cache;


class OfficerAssignmentModel extends Model
{
  protected $table = 'polling_station_officer';

  //protected $connection = 'booth_revamp';

  public static function officer_po_pro($data = array()){
     
     $election_id = Auth::user()->election_id;

      //STATE FUNCTION STARTS
      $sql_raw = "s.st_code,s.st_name,COUNT(DISTINCT(IF(role_id=34,p.ps_no,NULL))) total_po,COUNT(DISTINCT(IF(role_id=35,p.ps_no,NULL))) total_pro,COUNT(DISTINCT p.id ) as total_location,COUNT(DISTINCT psl.location_id) as total_mapped_location";

      $sql = DB::table('polling_station_location as p')
      ->join('m_election_details as e',[
            ['p.st_code', '=','e.st_code'],
      ])
      ->join('m_state as s',[
            ['p.st_code', '=','s.st_code'],
      ])
      ->join('boothapp_enable_acs as b',[
            ['b.st_code', '=','p.st_code'],
      ])
      ->join('m_ac as a',[ 
              ['a.ac_no', '=','p.ac_no'],
              ['a.st_code', '=','p.st_code'],
        ])
     
      ->leftjoin('polling_station_officer as psl',[
            ['p.id', '=','psl.location_id']
      ])

      ->leftjoin('polling_station_location_to_ps as pslp',[
            ['psl.id', '=','pslp.location_id']
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

        if(!empty($data['group_by'])){
          if($data['group_by']=='ac_no'){
              $sql->groupBy("a.ac_no")->groupBy("p.st_code");
          }else if($data['group_by']=='national'){
            
          }
        }else{
            $sql->groupBy("p.st_code");
        }

        if(!empty($data['order_by'])){
          if($data['group_by']=='ac_no'){
              $sql->orderByRaw("p.st_code, a.ac_no, a.ac_name ASC");
          }else if($data['order_by']=='national'){
            
          }
        }else{
            $sql->orderByRaw("p.st_code");
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


  public static function officer_po_pro_ac($data = array()){
    
    $election_id = Auth::user()->election_id;
    $sql_raw = "a.AC_NO as ac_no,a.AC_NAME as ac_name,COUNT(DISTINCT(IF(role_id=34,p.ps_no,NULL))) total_po,COUNT(DISTINCT(IF(role_id=35,p.ps_no,NULL))) total_pro,COUNT(DISTINCT p.id ) as total_location,COUNT(DISTINCT psl.location_id) as total_mapped_location";

         $sql = DB::table('boothapp_enable_acs as b')
        ->join('m_election_details as e',[
              ['b.st_code', '=','e.st_code'],
              ['b.ac_no', '=','e.CONST_NO'],
        ])
      
        ->join('m_ac as a',[ 
              ['a.ac_no', '=','b.ac_no'],
              ['a.st_code', '=','b.st_code'],
        ])
        ->leftjoin('polling_station_location as p',[
              ['b.ac_no', '=','p.ac_no'],
              ['b.st_code', '=','p.st_code'],
        ])
       
        ->leftjoin('polling_station_officer as psl',[
              ['p.id', '=','psl.location_id']
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

         $sql->groupBy("a.ac_no");
         $sql->orderByRaw("b.st_code, a.ac_no, a.ac_name ASC");
   

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


    //BLO COUNT
    public static function officer_blo($data = array()){

    	$election_id = Auth::user()->election_id;
 
    //STATE STARTS
      $sql_raw = "COUNT(DISTINCT(IF(role_id=33,p.ps_no,NULL))) total_blo";
      $sql = DB::table('polling_station_officer as p')
        ->join('boothapp_enable_acs as b',[
              ['b.st_code', '=','p.st_code'],
        ])
        ->join('m_election_details as e',[
              ['p.st_code', '=','e.st_code'],
        ])
        ->join('polling_station_location as psl',[
              ['p.location_id', '=','psl.id'],
        ])
        ->join('polling_station_location_to_ps as pslp',[
              ['psl.id', '=','pslp.location_id'],
        ]);


      $sql->selectRaw($sql_raw);

      if(!empty($data['st_code'])){
           $sql->where('p.st_code',$data['st_code']);
        }

        if(!empty($data['dist_no'])){
            $sql->where('a.DIST_NO_HDQTR',$data['dist_no']);
        }

        if(!empty($data['ac_no'])){
            $sql->where('p.ac_no',$data['ac_no']);
        }

        if(!empty($data['phase'])){
            if($data['phase']!='all'){
                $sql->where("e.PHASE_NO", $data['phase']);
            }else{
                $sql->whereIn("e.PHASE_NO", [1,2,3]);
            }
        }

        if(!empty($data['ac_no'])){
                $sql->groupBy("p.ac_no");
        }else{
            $sql->groupBy("p.st_code");
        }


        if(!empty($data['ac_no'])){
                $sql->orderByRaw("p.ac_no ASC");
        }else if(!empty($data['st_code'])){
            $sql->orderByRaw("p.st_code ASC");
        }


        $query = $sql->first();

        if(!$query){
            return $query =[];
        }
        return $query;


    //STATE ENDS    
          
    }


     public static function officer_blo_ac($data = array()){

      $election_id = Auth::user()->election_id;
      
      $sql_raw = "COUNT(DISTINCT(IF(role_id=33,p.ps_no,NULL))) total_blo";
      $sql = DB::table('polling_station_officer as p')
        ->join('boothapp_enable_acs as b',[
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

        $sql->groupBy("a.ac_no");
        $sql->orderByRaw("p.st_code, a.ac_no, a.ac_name ASC");

        $query = $sql->first();

        if(!$query){
            return $query =[];
        }
        return $query;

     }
 


    //SM COUNT
    public static function officer_sm($data = array()){

      $election_id = Auth::user()->election_id;
 
    //STATE STARTS
      $sql_raw = "COUNT(DISTINCT(ps_officer_id)) total_sm";
      $sql = DB::table('polling_station_officer as p')
        ->leftjoin('ps_sector_officer as pso',[
              ['p.id', '=','pso.ps_officer_id'],
              ['p.st_code', '=','pso.st_code'],
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

        if(!empty($data['ac_no'])){
                $sql->groupBy("p.ac_no");
        }else{
            $sql->groupBy("p.st_code");
        }


        if(!empty($data['ac_no'])){
                $sql->orderByRaw("p.ac_no ASC");
        }else if(!empty($data['st_code'])){
            $sql->orderByRaw("p.st_code ASC");
        }


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

    //STATE ENDS
            
    }
 

     public static function officer_sm_ac($data = array()){

      $election_id = Auth::user()->election_id;
      
      $sql_raw = "COUNT(DISTINCT(ps_officer_id)) total_sm";
      $sql = DB::table('polling_station_officer as p')
        ->leftjoin('ps_sector_officer as pso',[
              ['p.id', '=','pso.ps_officer_id'],
              ['p.st_code', '=','pso.st_code'],
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

        $sql->groupBy("a.ac_no");
        $sql->orderByRaw("p.st_code, a.ac_no, a.ac_name ASC");


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
 


}