<?php 
 namespace App\models\Counting;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class CountingResultsPublishModel extends Model
{
    
    protected $table ="counting_results_publish";
    protected $primaryKey ='id'; 
 
    protected $fillable = ['st_code','election_id','pc_no','ac_no','round_id','certificate','agree','added_create_at','created_by','updated_at','added_update_at','updated_by','transactiontime','created_at','roname','name'];
   
   public static function add_records($data = array()){
              date_default_timezone_set('Asia/Kolkata');
                $datetime = date("Y-m-d H:i:s");
                $date = date("Y-m-d");
              $object = new CountingResultsPublishModel();
              $object->st_code  = $data['st_code'];
              $object->election_id = $data['election_id'];
              $object->pc_no = $data['pc_no'];
              $object->ac_no = $data['ac_no']; 
              $object->round_id = $data['round_id']; 
              $object->certificate = $data['certificate']; 
              $object->agree = $data['agree']; 
              $object->created_at =$datetime; 
              $object->added_create_at =$date;
              $object->roname = $data['roname'];
              $object->name = $data['name']; 
              $object->created_by = Auth::user()->officername;

              return $object->save();
          }

    
}