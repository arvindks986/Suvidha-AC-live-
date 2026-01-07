<?php 
namespace App\models\Counting;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class BoothCountingModel extends Model
{
  public  function checkmasterrecords($data = array()){
    
            $sql_raw = "complete_round,finalized_round";
            $sql = DB::table($data['table'])->selectRaw($sql_raw);

    if(!empty($data['election_id'])){
              $sql->where("election_id",$data['election_id']);
          }
    if(!empty($data['ac_no'])){
                $sql->where("ac_no", $data['ac_no']);
              }
                $query = $sql->first();

    return $query;

  }  
  
  public  function countpollingstation($data = array()){

            $sql_raw = "count(*) as cnt";
            $sql = DB::table('polling_station')->selectRaw($sql_raw);

            if(!empty($data['st_code'])){
                      $sql->where("ST_CODE",$data['st_code']);
                  }
            if(!empty($data['ac_no'])){
                        $sql->where("AC_NO", $data['ac_no']);
                      }
            // if(!empty($data['election_id'])){
            //       $sql->where("election_id", $data['election_id']);
            //     }
            $query = $sql->first();
     
            if(isset($query)){ 
                  return $query->cnt;
            }else{
                return 0;
              }

  }
  public  function get_table_master_details($data = array()){
    
    $sql_raw = "id, election_id, election_typeid, st_code,ac_no,pc_no,total_no_ps,total_no_tables,total_no_rounds,complete_rounds,created_at";
    
    $sql = DB::table('table_master')->selectRaw($sql_raw);

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

  public  function manage_table_rounds($tableno,$roundsno,$ele_details){
    
   for($i=1;$i<=$tableno;$i++)
   {
    $name="Table".$i;
    $ins_data = array('table_name'=>$name,
      'election_id'=>$ele_details->ELECTION_ID,
      'election_typeid'=>$ele_details->ELECTION_TYPEID,
      'created_at'=>date('Y-m-d H:i:s'),
      'added_create_at'=>date('Y-m-d')); 
    $sq = DB::table('m_table')->where('table_name',$name)->where('id',$i)->first();
    
    if(isset($sq)) {
      insertData('m_table', $ins_data);
    }
    
  }
  for($i=1;$i<=$roundsno;$i++)
  {
    $name="Rounds".$i;
    $ins_data = array('round_name'=>$name,
      'election_id'=>$ele_details->ELECTION_ID,
      'election_typeid'=>$ele_details->ELECTION_TYPEID,
      'created_at'=>date('Y-m-d H:i:s'),
      'added_create_at'=>date('Y-m-d')); 
    $sq = DB::table('m_round')->where('round_name',$name)->where('id',$i)->first();
    if(isset($sq)) {
      insertData('m_round', $ins_data);
    }
    
  }
  return true;

} 
public function roundsechudle($data = array())
{       
 
  $sql = DB::table('round_master')->select('*');

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

public  function getcompletetables($data = array()){
  
  $sql_raw = "id, table_id";
  
  $sql = DB::table($data['table_name'])->selectRaw($sql_raw);

  if(!empty($data['election_id'])){
    $sql->where("election_id", $data['election_id']);
  }
        // if(!empty($data['st_code'])){
        //   $sql->where("st_code", $data['st_code']);
        // }
        //  if(!empty($data['pc_no'])){
        //   $sql->where("pc_no", $data['pc_no']);
        // }
  if(!empty($data['ac_no'])){
    $sql->where("ac_no", $data['ac_no']);
  }
  if(!empty($data['round_id'])){
    $sql->where("round_id", $data['round_id']);
  }
  $sql->groupBy('table_id');
  $query = $sql->get();

  return $query;

}

public  function getvotedetailsbyroundid($data = array()){


 if(empty($data['table_id'])){
  return [];
} 
$sql_raw = "id,table_id,nom_id,candidate_id,party_id,election_id,evm_vote";

$sql = DB::table($data['table_name'])->selectRaw($sql_raw);

if(!empty($data['election_id'])){
  $sql->where("election_id", $data['election_id']);
}

if(!empty($data['ac_no'])){
  $sql->where("ac_no", $data['ac_no']);
}
if(!empty($data['round_id'])){
  $sql->where("round_id", $data['round_id']);
}
if(!empty($data['table_id'])){
  $sql->where("table_id", $data['table_id']);
}
$query = $sql->get();

if(($query) and count($query)>0){    
 foreach ($query as $key => $value) {
   $result[$key] = [
    'id'              =>$value->id, 
    'table_id'        =>$value->table_id, 
    'nom_id'          =>$value->nom_id,
    'candidate_id'    =>$value->candidate_id, 
    'election_id'     => $value->election_id,
    'evm_vote'        => $value->evm_vote,
  ];
}  
return $result;   

} else {
  return [];
}


}

public  function getpollingstationgroupby($data = array()){ 
   
 $result = [
  'table_id' =>'', 
  'ps_no' =>'', 
  'bu_no' =>'',
  'cu_no'  =>'',  
  'vvpat_no'  => '', 
  'rejected_vote'  => '',
  'tendered_vote'  => '',
  'part_no'  => '',  
];

if(empty($data['table_id'])){
  return $result;
}
$sql_raw = "table_id,ps_no,bu_no,cu_no,vvpat_no,rejected_vote,tendered_vote,part_no,cu_defect_id,vvpat_defect_id,results";

$sql = DB::table($data['table_name'])->selectRaw($sql_raw);

if(!empty($data['election_id'])){
  $sql->where("election_id", $data['election_id']);
}

         if(!empty($data['pc_no'])){
          $sql->where("pc_no", $data['pc_no']);
        }
if(!empty($data['ac_no'])){
  $sql->where("ac_no", $data['ac_no']);
}
if(!empty($data['round_id'])){
  $sql->where("round_id", $data['round_id']);
}
if(!empty($data['table_id'])){
  $sql->where("table_id", $data['table_id']);
}
$sql->groupBy('round_id')->groupBy('table_id');
$query = $sql->first(); 
//dd($query); 
if(($query) and isset($query)){  
 
  $result = [
    'table_id'      =>$query->table_id, 
    'ps_no'         =>$query->ps_no, 
    'bu_no'         =>$query->bu_no,
    'cu_no'         =>$query->cu_no, 
    'vvpat_no'      => $query->vvpat_no,
    'rejected_vote' => $query->rejected_vote,
    'tendered_vote' => $query->tendered_vote,
    'part_no'       => $query->part_no,
    'vvpat_defect_id'  => $query->vvpat_defect_id,
    'cu_defect_id'     => $query->cu_defect_id,
    'results'          => $query->results,
  ];
  
}
return   $result;

}
public  function getpswiserecord($data = array()){ 
 $sql = DB::table($data['table_name'])->select('*');

 if(!empty($data['election_id'])){
  $sql->where("election_id", $data['election_id']);
}
if(!empty($data['ac_no'])){
  $sql->where("ac_no", $data['ac_no']);
}
if(!empty($data['round_id'])){
  $sql->where("round_id", $data['round_id']);
}
if(!empty($data['table_id'])){
 $sql->where("table_id", $data['table_id']);
}
if(!empty($data['nom_id'])){
 $sql->where("nom_id", $data['nom_id']);
}
if(!empty($data['candidate_id'])){
 $sql->where("candidate_id", $data['candidate_id']);
}

$query = $sql->first();  
return    $query;

}  

public function get_details($data = array()){
 $sub_query  = "";
 
 $sub_query .= "nom_id,ac_no,candidate_name, party_name,party_id";
 $object = DB::table("counting_master_".strtolower($data['st_code']))->selectRaw($sub_query)->where('ac_no',$data['ac_no'])->orderBy('id','ASC')->get();
 return $object;
} 
public static function tabulating_trend($data = array()){
                  if($data['round_id']=='' || $data['round_id']==0)
                  {
                   return false; 
                 }
                 if(empty($data['st_code'])){
                  return false;
                }
                else{ 
                 $table2="counting_master_".strtolower($data['st_code']);
                 $table1="counting_ps_".strtolower($data['st_code']);
                }
                if(!empty($data['total_no_tables'])){
                 $nooftables=$data['total_no_tables'];
                }
                $sql_row="t1.nom_id,t1.candidate_id,t2.candidate_name , t2.party_name";
                if(!empty($data['round_id'])){
                  for($i=1;$i<=$nooftables;$i++) {
                    $sql_row .=", SUM(IF(t1.table_id=".$i.",t1.evm_vote,null)) as table".$i."";
                  } 
                } 
                $sql_row .=", IFNULL(SUM(evm_vote),0) as total";


                $sql = DB::table($table1 .' as t1')->join($table2 .' as t2',[
                  ['t1.nom_id','=','t2.nom_id'],  ['t1.election_id','=','t2.election_id']
                ])->selectRaw($sql_row);

                if(!empty($data['round_id'])){
                  $sql->where('t1.round_id',$data['round_id']);
                } 
                if(!empty($data['ac_no'])){
                  $sql->where('t1.ac_no',$data['ac_no']);
                } 
                if(!empty($data['election_id'])){
                  $sql->where('t1.election_id',$data['election_id']);
                } 
                        
                 
                $query = $sql->groupBy('t1.nom_id')->orderBy('t2.id')->get(); 
               
                return $query;

            }   // end function 

            public static function grandtotal_tabulating_trend_columwise($data = array()){
             
             if(empty($data['st_code'])){
              return false;
            }
            else{ 
             $table2="counting_master_".strtolower($data['st_code']);
             $table1="counting_ps_".strtolower($data['st_code']);
           }
           if(!empty($data['total_no_tables'])){
             $nooftables=$data['total_no_tables'];
           }
           $sql_row="t1.nom_id,t1.candidate_id,t2.candidate_name , t2.party_name";
           if(!empty($data['round_id'])){
            for($i=1;$i<=$nooftables;$i++) {
              $sql_row .=", SUM(IF(t1.table_id=".$i.",t1.evm_vote,0)) as table".$i."";
            } 
          } 
          $sql_row .=", IFNULL(SUM(evm_vote),0) as total";

          
          $sql = DB::table($table1 .' as t1')->join($table2 .' as t2',[
            ['t1.nom_id','=','t2.nom_id'],  ['t1.election_id','=','t2.election_id']
          ])->selectRaw($sql_row);

          if(!empty($data['round_id'])){
            $sql->where('t1.round_id',$data['round_id']);
          } 
          if(!empty($data['ac_no'])){
            $sql->where('t1.ac_no',$data['ac_no']);
          } 
          if(!empty($data['election_id'])){
            $sql->where('t1.election_id',$data['election_id']);
          } 
          
          $query = $sql->first(); 

          return $query;
          
            }   // end function 
// 
            public function get_previous_total($data = array()){
             $sub_query  = "";
             $sub_sql  = []; 
             if($data['round']!=0)
              $previous_round = $data['round'] - 1;
            else
              $previous_round=0;
            if($previous_round != 0){
             for($i = $previous_round; $i > 0; $i--) {
               $sub_sql[] = "IFNULL(round".$i.",0)";
             }

             $round_sql = implode('+',$sub_sql);
             if($round_sql){
               $sub_query .= $round_sql." AS previous_total";
             }
           }else{
             $sub_query = "0 AS previous_total";
           }

           $sub_query .= ", table1.nom_id, table1.ac_no, table1.candidate_name, table1.party_name, table1.party_id";
           $object = DB::table("counting_master_".strtolower($data['st_code'])." as table1")->selectRaw($sub_query)->where('table1.ac_no',$data['ac_no'])->groupBy('table1.ac_no')->groupBy('table1.nom_id')->orderBy('table1.id','ASC')->get();
           return $object;
         } 

         public function  get_acwisepollingstation($data = array()){
          
           $sql_row ="CCODE,ST_CODE,AC_NO,PART_NO,PS_NO,PS_NAME_EN";
           
           $sql = DB::table('polling_station')->selectRaw($sql_row);

           if(!empty($data['st_code'])){
            $sql->where('ST_CODE',$data['st_code']);
          } 
          if(!empty($data['ac_no'])){
            $sql->where('AC_NO',$data['ac_no']);
          }       
                //ORDER BY STARTS
          $sql->orderByRaw("CONVERT(PS_NO,INT) ASC");
              //ORDER BY ENDS
          $query = $sql->get(); 

          return $query;
        } 
        public function  getbypsno($data = array()){
          
         $sql_row ="CCODE,ST_CODE,AC_NO,PART_NO,PS_NO,PS_NAME_EN";
         
         $sql = DB::table('polling_station')->selectRaw($sql_row);

         if(!empty($data['st_code'])){
          $sql->where('ST_CODE',$data['st_code']);
        } 
        if(!empty($data['ac_no'])){
          $sql->where('AC_NO',$data['ac_no']);
        }
        if(!empty($data['ps_no'])){
          $sql->where('PS_NO',$data['ps_no']);
        }        
        
        $query = $sql->first(); 
        return $query;
      } 
      public static function  round_wise_calculate_vote($data = array()){
       
       $sql_row ="nom_id,candidate_id,ac_no,election_id,results,IFNULL(SUM(evm_vote),0) AS totalevmvote, IFNULL(SUM(rejected_vote),0) AS totalrejectedvote,IFNULL(SUM(tendered_vote),0) AS totaltendredvote";
       
       $sql = DB::table("counting_ps_".strtolower($data['st_code']))->selectRaw($sql_row);
       
       if(!empty($data['ac_no'])){
        $sql->where('ac_no',$data['ac_no']);
      }
      if(!empty($data['election_id'])){
        $sql->where('election_id',$data['election_id']);
      }        
      if(!empty($data['round'])){
        $sql->where('round_id',$data['round']);
      }
      //$sql->where('results','0');
      $sql->groupby('nom_id');   
      $query = $sql->get(); 
      return $query;
    }
    public static function noofcandidate($data =array())
    {
      $sql_row ="count(*) as cnt";
      $sql = DB::table("counting_master_".strtolower($data['st_code']))->selectRaw($sql_row);
      
      if(!empty($data['ac_no'])){
        $sql->where('ac_no',$data['ac_no']);
      }
      if(!empty($data['election_id'])){
        $sql->where('election_id',$data['election_id']);
      }        
      $sql->where('party_id','!=','1180');
      $query = $sql->first(); 
      if(isset($query))
            return $query->cnt; 
      else 
           return 0; 
    }
    public static function getallcandidate($data =array())
    {
      $sql_row ="nom_id,candidate_id,candidate_name,party_id,party_abbre,party_name";
      $sql = DB::table("counting_master_".strtolower($data['st_code']))->selectRaw($sql_row);
      
      if(!empty($data['ac_no'])){
        $sql->where('ac_no',$data['ac_no']);
      }
      if(!empty($data['election_id'])){
        $sql->where('election_id',$data['election_id']);
      }        
      $sql->where('party_id','!=','1180');
      //$sql->orderByRaw('id','ASC');
      $sql->orderByRaw('id');
      $query = $sql->get(); 
      
      return $query; 
    }
     public static function getallvotesbypsno($data =array())
        {
          $sql_row ="t2.nom_id,t2.candidate_id,t2.party_id,t2.evm_vote,t2.rejected_vote,t2.tendered_vote";
          $sql = DB::table("counting_master_".strtolower($data['st_code'])." as t1")
                ->join("counting_ps_".strtolower($data['st_code'])." as t2",
                  [
                ['t1.nom_id','=','t2.nom_id'],  ['t1.ac_no','=','t2.ac_no']
              ]) ->selectRaw($sql_row);
          
          if(!empty($data['ac_no'])){
            $sql->where('t2.ac_no',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('t2.election_id',$data['election_id']);
          }
           if(!empty($data['ps_no'])){
            $sql->where('t2.ps_no',$data['ps_no']);
          }          
          //$sql->where('party_id','!=','1180');
          $sql->orderByRaw('t1.id','ASC');
          $query = $sql->get(); 
          
          return $query; 
        }
    public static function totalelectors($data =array())
        {
          $sql_row ="ac_no,SUM(electors_total+electors_service) AS total";
          $sql = DB::table("electors_cdac")->selectRaw($sql_row);
          if(!empty($data['st_code'])){
            $sql->where('st_code',$data['st_code']);
          }  
          if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('election_id',$data['election_id']);
          }
                   
          $query = $sql->first(); 
           if(isset( $query))
                return $query;
          else 
              return 0; 

        }
     public static function getallpsvotes($data =array())
        {
        $sql_row ="ps.ST_CODE,ps.AC_NO,ps.PS_NO,ps.PS_NAME_EN,cm.nom_id,cm.candidate_id,cm.party_id,
                        IFNULL(cps.evm_vote,0) AS evm_vote,IFNULL(cps.rejected_vote,0) AS rejected_vote,
                        IFNULL(cps.tendered_vote,0) AS tendered_vote";

          $sql = DB::table("polling_station as ps")
                ->leftJoin("counting_master_".strtolower($data['st_code'])." as cm",[ ['ps.AC_NO','=','cm.ac_no'] ])
                ->leftJoin("counting_ps_".strtolower($data['st_code'])." as cps",
                  [ 
                  ['ps.AC_NO','=','cps.ac_no'], ['ps.PS_NO','=','cps.ps_no'], ['cps.nom_id','=','cm.nom_id']
                  ]) ->selectRaw($sql_row);
          
          if(!empty($data['st_code'])){
            $sql->where('ps.ST_CODE',$data['st_code']);
          }
           if(!empty($data['ac_no'])){
            $sql->where('ps.AC_NO',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('cm.election_id',$data['election_id']);
          }
           if(!empty($data['ps_no'])){
            $sql->where('ps.ps_no',$data['ps_no']);
          }          
          //$sql->where('party_id','!=','1180');  ORDER BY CONVERT(ps.PS_NO,INT) ASC ,cm.id ASC
          $sql->orderByRaw("CONVERT(ps.PS_NO,INT) ASC");
          $sql->orderBy('cm.id','ASC');
          $query = $sql->get(); 
          
          return $query; 
        } // end function

      public static function getpsvotessum($data =array())
        {
        $sql_row ="cm.nom_id,cm.candidate_id,cm.party_id,IFNULL(SUM(cps.evm_vote),0) AS evm_vote,
                  IFNULL(SUM(cps.rejected_vote),0) AS rejected_vote,IFNULL(SUM(cps.tendered_vote),0) AS tendered_vote";

          $sql = DB::table("polling_station as ps")
                ->leftJoin("counting_master_".strtolower($data['st_code'])." as cm",[ ['ps.AC_NO','=','cm.ac_no'] ])
                ->Join("counting_ps_".strtolower($data['st_code'])." as cps",
                  [ 
                  ['ps.AC_NO','=','cps.ac_no'], ['ps.PS_NO','=','cps.ps_no'], ['cps.nom_id','=','cm.nom_id']
                  ]) ->selectRaw($sql_row);
          
          if(!empty($data['st_code'])){
            $sql->where('ps.ST_CODE',$data['st_code']);
          }
           if(!empty($data['ac_no'])){
            $sql->where('ps.AC_NO',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('cm.election_id',$data['election_id']);
          }
                   
          $sql->groupby("cps.nom_id");
          $sql->orderBy('cm.id','ASC');
          $query = $sql->get(); 
          
          return $query; 
        }
     public static function get_counting_tendered_vote($data =array())
        {
        $sql_row ="id,rejected_vote,tendered_vote,created_at,updated_at,month,year";

          $sql = DB::table("counting_ps_tendered")->selectRaw($sql_row);
          
          if(!empty($data['st_code'])){
            $sql->where('st_code',$data['st_code']);
          }
           if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('election_id',$data['election_id']);
          }
          if(!empty($data['ps_no'])){
            $sql->where('ps_no',$data['ps_no']);
          }

          $query = $sql->first(); 
          
          return $query; 
        }
    public static function get_roundwise_psnumber($data =array())
        {
          $results=array();
          for($i=1; $i<=$data['total_no_tables']; $i++)
              {
                 $field="ps".$i;
                 $results[$field] ='';
              }
         
        $sql_row ="table_id,ps_no";

          $sql = DB::table("counting_ps_".strtolower($data['st_code']))->selectRaw($sql_row);
          
           if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('election_id',$data['election_id']);
          }
          if(!empty($data['round_id'])){
            $sql->where('round_id',$data['round_id']);
          }
           $sql->groupby('ps_no');
          $query = $sql->get();  //dd($query);
         if(isset( $query)) { 
              foreach ($query as  $val) {
                        for($i=1; $i<=$data['total_no_tables']; $i++)  {
                               
                              if( $val->table_id==$i ){
                                $psname=getpollingstationname($data['st_code'],$data['ac_no'],$val->ps_no);
                                   $field="ps".$i;
                                   $namefield="psname".$i;
                                  //$results[$field] =$val->ps_no."-".$psname['PS_NAME_EN'];
                                   $results[$field] =$val->ps_no;
                                    $results[$namefield] =$psname['PS_NAME_EN'];
                                  break;
                                }   
                            }
                      }
              }
           return $results;

        }
    public static function roundwiseresults($data =array()){
        DB::enableQueryLog();
          $sql_row ="round_id,min(results) as results";
          if(empty($data['table'])){
             return false;
          }
          $sql = DB::table($data['table'])->selectRaw($sql_row);
          
           if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('election_id',$data['election_id']);
          }
          if(!empty($data['election_typeid'])){
            $sql->where('election_typeid',$data['election_typeid']);
          }
          
          //  $sql->where('results','0')->orwhere('results','1');
          
           $sql->groupby('round_id');
           $sql->orderBy('round_id','ASC');
          // $sql->orderBy('results','ASC');
          $query = $sql->get();
          // $query = DB::getQueryLog();
             
          //   dd($query);   
          // dd($query);
          return $query;
        }
    public static function maxidoftable($data =array()){
          $sql_row ="max(id) as mid";
          if(empty($data['table'])){
             return false;
          }
          $sql = DB::table($data['table'])->selectRaw($sql_row);
          
           if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
            }
          if(!empty($data['election_id'])){
              $sql->where('election_id',$data['election_id']);
              }

           $query = $sql->first();   

          return $query->mid;
        }
 public static function getassigntable($data=array()){  
      $results=[
          'assigntable'=>'',
          'countassigntable'=>0,
      ];
      $sql_raw = "GROUP_CONCAT(table_no) AS table_no";
      $sql = DB::table('counting_users_table_details')->selectRaw($sql_raw);
    
        if(!empty($data['st_code'])){
                $sql->where("st_code", $data['st_code']);
              }
        if(!empty($data['election_id'])){
              $sql->where("election_id",$data['election_id']);
          }
        if(!empty($data['ac_no'])){
                $sql->where("ac_no",$data['ac_no']);
              }
        if(!empty($data['users_name'])){
                $sql->where("users_name",$data['users_name']);
              }
        $sql->where("deleted",'0');
         $sql->groupBy('ac_no');   
         
         $query = $sql->first();
        
       if(isset($query)) {
              $results=[
                'assigntable'=>array_sort(explode(",",$query->table_no)),
                'countassigntable'=>count(explode(",",$query->table_no)),
            ];
          }
        
        return $results;
   }

   public  function completetable($data = array()){  
                 //DB::enableQueryLog();
                $sql_raw = "COUNT(DISTINCT(`table_id`)) AS cnt";
                $sql = DB::table('counting_ps_'.strtolower($data['st_code']))->selectRaw($sql_raw);

                 if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                }
                if(!empty($data['ac_no'])){
                  $sql->where("ac_no", $data['ac_no']);
                }
                if(!empty($data['round_id'])){
                  $sql->where("round_id", $data['round_id']);
                }
                $query = $sql->first();
                //  $query = DB::getQueryLog();
                // dd($query);   
                return    $query->cnt;

          }
  public  function loginuserassigntable($data = array()){  
               //  DB::enableQueryLog();
                $sql_raw = "COUNT(DISTINCT(`table_id`)) AS cnt";
                $sql = DB::table('counting_ps_'.strtolower($data['st_code']))->selectRaw($sql_raw);

                 if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                }
                if(!empty($data['ac_no'])){
                  $sql->where("ac_no", $data['ac_no']);
                }
                if(!empty($data['round_id'])){
                  $sql->where("round_id", $data['round_id']);
                }
                
                $sql->where("created_by", Auth::user()->officername);
                
                $query = $sql->first();
                //  $query = DB::getQueryLog();
                // dd($query);   
                return    $query->cnt;

          }    
  public static function checkpublish($data =array()){
         
          $sql_row ="round_id,min(results) as results";
          if(empty($data['table_name'])){
             return false;
          }
          $sql = DB::table($data['table_name'])->selectRaw($sql_row);
          
           if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('election_id',$data['election_id']);
          }
          if(!empty($data['election_typeid'])){
            $sql->where('election_typeid',$data['election_typeid']);
          }
           if(!empty($data['round_id'])){
            $sql->where('round_id',$data['round_id']);
          }
            
          $query = $sql->first();
           
          return $query->results;
        }
     public static function get_allpollingstation($data =array())
        {
          $results=array();
          for($i=1; $i<=$data['total_no_tables']; $i++)
              {
                 $field="ps".$i;
                 $results[$field] ='';
              }
         
        $sql_row ="table_id,ps_no";

          $sql = DB::table("counting_ps_".strtolower($data['st_code']))->selectRaw($sql_row);
          
           if(!empty($data['ac_no'])){
            $sql->where('ac_no',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('election_id',$data['election_id']);
          }
          
           $sql->groupby('ps_no');
          $query = $sql->get();  //echo "<pre>"; print_r($query); echo "</pre>";
         if(isset( $query)) { $i=0;
              foreach ($query as  $val) {   $i++;
                                  $field="ps".$i;
                                  $results[$field] =$val->ps_no;
                              }
                      }
               
           return $results;

        }
    public static function get_allpostalvotes($data =array())
        {
            $sql_row ="a.nom_id,a.candidate_id,a.party_id,a.candidate_name,a.postalballot_vote,a.total_vote,r.rejected_votes,r.postal_total_votes,r.tended_votes";

          $sql = DB::table("counting_master_".strtolower($data['st_code'])." as a")
                 ->Join("round_master as r",  [ 
                  ['a.ac_no','=','r.ac_no'], ['a.election_id','=','r.election_id']
                   ]) ->selectRaw($sql_row);
          
          if(!empty($data['st_code'])){
            $sql->where('r.st_code',$data['st_code']);
          }
           if(!empty($data['ac_no'])){
            $sql->where('a.ac_no',$data['ac_no']);
          }
          if(!empty($data['election_id'])){
            $sql->where('a.election_id',$data['election_id']);
          }
          $sql->orderBy('a.id','ASC');
          $query = $sql->get(); 
          
          return $query;   
         

        }
    public  function check_pollingstation($data = array()){ 
                   $sql = DB::table($data['table_name'])->select('*');

                   if(!empty($data['election_id'])){
                    $sql->where("election_id", $data['election_id']);
                  }
                  if(!empty($data['ac_no'])){
                    $sql->where("ac_no", $data['ac_no']);
                  }
                  if(!empty($data['ps_no'])){
                    $sql->where("ps_no", $data['ps_no']);
                  }
                   
                  $query = $sql->first();  
                  return    $query;

        }  
    public function getresultsuploads($data = array()){ 
              $sql = DB::table("counting_results_pdf")->select('*');
                  
                  if(!empty($data['st_code'])){
                    $sql->where("st_code", $data['st_code']);
                  }
                  if(!empty($data['ac_no'])){
                    $sql->where("ac_no", $data['ac_no']);
                  }
                   if(!empty($data['election_id'])){
                    $sql->where("election_id", $data['election_id']);
                  }
                  
                  if(!empty($data['round_id'])){
                    $sql->where("round_id", $data['round_id']);
                  }
                   
                  $query = $sql->first();  
                  return    $query;

          }
}  // end class 

 
