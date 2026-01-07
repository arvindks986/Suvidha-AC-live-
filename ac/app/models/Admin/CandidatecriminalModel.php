<?php 
namespace App\models\Admin;
use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;

class CandidatecriminalModel extends Model
{

  protected $table = 'candidate_criminaluploads';

  
  public $fillable = ['id','candidate_id','nom_id','st_code','pc_no','ac_no','election_id','path','name',
                      'added_create_at','created_at','created_by','added_update_at','updated_at',
                      'updated_by','transactiontime'];


  public static function get_allrecords($st_code,$ac_no,$election_id){
    $object = CandidatecriminalModel::where('st_code',$st_code)->where('ac_no',$ac_no)
                ->where('election_id',$election_id)->get();
    if(!$object){
      return false;
    }
    return $object->toArray();

	}

 
 }