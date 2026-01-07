<?php 
namespace App\models\Counting;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class UsercountingModel extends Model
{
   public static function getalluser($data=array()){
    
      $sql_raw = "id,officername,designation,placename,name,st_code, dist_no, ac_no,pc_no,is_active,role_id,Phone_no,email";
      $sql = DB::table('officer_login')->selectRaw($sql_raw);
    
        if(!empty($data['st_code'])){
                $sql->where("st_code", $data['st_code']);
              }
        if(!empty($data['election_id'])){
              $sql->where("election_id",$data['election_id']);
          }
        if(!empty($data['ac_no'])){
                $sql->where("ac_no", $data['ac_no']);
              }
              $sql->whereIn('role_id', [19, 36]);
        
        //$sql->where("designation",'<>','ROAC');
        $sql->orderBY('id');          
        $query = $sql->get();
      
       return $query;
   }
   public static function getalluserbyparentid($data=array()){
    
      $sql_raw = "id,officername,designation,placename,name,st_code, dist_no, ac_no,pc_no,is_active,role_id,Phone_no,email";
      $sql = DB::table('officer_login')->selectRaw($sql_raw);
    
        if(!empty($data['st_code'])){
                $sql->where("st_code", $data['st_code']);
              }
        if(!empty($data['election_id'])){
              $sql->where("election_id",$data['election_id']);
          }
        if(!empty($data['ac_no'])){
                $sql->where("ac_no", $data['ac_no']);
              }
        $sql->where("parent_id",'=',$data['id']);
        
        $sql->orderBY('id');          
        $query = $sql->get();
      
       return $query;
   }
   public static function countcountinguser($data=array()){
    
      $sql_raw = "count(*) as cnt";
      $sql = DB::table('officer_login')->selectRaw($sql_raw);
    
        if(!empty($data['st_code'])){
                $sql->where("st_code", $data['st_code']);
              }
        if(!empty($data['election_id'])){
              $sql->where("election_id",$data['election_id']);
          }
        if(!empty($data['ac_no'])){
                $sql->where("ac_no", $data['ac_no']);
              }
         $sql->where("parent_id",'=',$data['id']);
         
        $query = $sql->first();
        return $query->cnt;
   }
  public static function getalluserbytablesdetails($data=array()){
    
      $sql_raw = "id,st_code,ac_no,users_name,table_no,created_at";
      $sql = DB::table('counting_users_table_details')->selectRaw($sql_raw);
    
        if(!empty($data['st_code'])){
                $sql->where("st_code", $data['st_code']);
              }
        if(!empty($data['election_id'])){
              $sql->where("election_id",$data['election_id']);
          }
        if(!empty($data['ac_no'])){
                $sql->where("ac_no", $data['ac_no']);
              }

        $sql->where("deleted",'0');
          
         $sql->orderBY('users_name','ASC');   //deleted  
        $query = $sql->get();
        return $query;
   }

  public static function getallassigntable($data=array()){  
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
}  // end class

 