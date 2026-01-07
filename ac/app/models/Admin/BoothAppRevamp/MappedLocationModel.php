<?php 
namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB, Cache;


class MappedLocationModel extends Model
{
  protected $table = 'polling_station_officer';
  
  
   public static function get_unmapped_ps($filter = array()){
    $total  = 0;
    $sql = \DB::table("boothapp_polling_station as p")->join("m_election_details",[
      ["m_election_details.ST_CODE","=","p.ST_CODE"],
      ["m_election_details.CONST_NO","=","p.AC_NO"],
    ])->leftjoin("polling_station_location_to_ps as pl",[
      ['p.ST_CODE','=','pl.st_code'],
      ['p.AC_NO','=','pl.ac_no'],
	  ['p.PS_NO','=','pl.ps_no'],
    ])->join('boothapp_enable_acs', [
            ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
            ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
        ])->selectRaw("COUNT(IF(pl.ps_no IS NULL, 1, NULL)) as total");

    $sql->where('CONST_TYPE','AC');
		
    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('boothapp_enable_acs.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('boothapp_enable_acs.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('boothapp_enable_acs.ac_no',$filter['ac_no']);
    }

    $query = $sql->first();
    if($query){
      $total = $query->total;
    }
    return $total;
  }
  
  public static function get_unmapped_pswise($filter = array()){

	
    $total  = 0;
	$sql = \DB::table("boothapp_polling_station as p")->join("m_election_details",[
      ["m_election_details.ST_CODE","=","p.ST_CODE"],
      ["m_election_details.CONST_NO","=","p.AC_NO"],
    ])->leftjoin("polling_station_location_to_ps as pl",[
      ['p.ST_CODE','=','pl.st_code'],
      ['p.AC_NO','=','pl.ac_no'],
	  ['p.PS_NO','=','pl.ps_no'],
    ])->join('boothapp_enable_acs', [
            ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
            ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
        ])->selectRaw("p.PS_NAME_EN,p.PS_NO");

    $sql->where('CONST_TYPE','AC');
	$sql->whereNull("pl.ps_no");

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('boothapp_enable_acs.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('boothapp_enable_acs.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('boothapp_enable_acs.ac_no',$filter['ac_no']);
    }

    $query = $sql->get();
    return $query;  
  }
  
  public static function get_unmapped_location($filter = array()){
    $total  = 0;
    $sql_raw = "p.id,p.name";
	$sql = DB::table('polling_station_location as p')
	  ->join('m_election_details as e',[
			['p.st_code', '=','e.st_code'],
			['p.ac_no', '=','e.CONST_NO']
	  ])
	  ->join('boothapp_enable_acs as b',[
			['b.st_code', '=','p.st_code'],
			['b.ac_no', '=','p.AC_NO'],
	  ])->leftjoin('polling_station_officer as psf',[
			['p.id', '=','psf.location_id'],
      ['p.st_code', '=','psf.st_code'],
			['p.ac_no', '=','psf.ac_no']
	  ]);
	  $sql->selectRaw($sql_raw);

    $sql->where('CONST_TYPE','AC');

    $sql->whereNull("psf.location_id");
		
    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('b.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('b.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('b.ac_no',$filter['ac_no']);
    }

    $query = $sql->get();
	//dd($query);
    return $query;  
  }
  
  //protected $connection = 'booth_revamp';
  public static function officer_mapped_location($data = array()){
     $election_id = Auth::user()->election_id;
      //STATE FUNCTION STARTS
      $sql_raw = "COUNT(DISTINCT p.id) as total_location, COUNT(DISTINCT psf.location_id) as total_mapped_location";
      $sql = DB::table('polling_station_location as p')
      ->join('m_election_details as e',[
            ['p.st_code', '=','e.st_code'],
			['p.ac_no', '=','e.CONST_NO']
      ])
      ->join('boothapp_enable_acs as b',[
            ['b.st_code', '=','p.st_code'],
			['b.ac_no', '=','p.AC_NO'],
      ])
      ->leftjoin('polling_station_officer as psf',[
            ['p.id', '=','psf.location_id'],
			['p.ac_no', '=','psf.ac_no']
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
              ['p.id', '=','psl.location_id'],
			  ['p.ac_no', '=','psl.ac_no']
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
 


}