<?php 
namespace App\models\Admin\mparty;
use Illuminate\Database\Eloquent\Model;
use DB;
class MpartysymbollogModel extends Model
{
  protected $table = 'm_party_symbol_log';
  public $fillable = ['id','ccode','partyabbre','symbolno','symbol','created_at','updated_at','added_created_at','added_updated_at','created_by','updated_by'];

public static function add_data($data = array()){ //dd($data);
    
      $object = new MpartysymbollogModel();
      $object->ccode = $data['ccode']; 
      $object->partyabbre = $data['partyabbre']; 
      $object->symbolno = $data['symbolno']; 
      if($data['symbol']!='')
          $object->symbol = $data['symbol']; 
      $object->created_at = date('Y-m-d H:i:s');
      $object->added_created_at =date('Y-m-d');
      $object->created_by =\Auth::user()->officername;
    
      $object->save();
      return true;
  }

  
  public static function get_all($data = array()){
    $sql_raw = "*";
    $sql = MpartysymbollogModel::selectRaw($sql_raw);
    $sql->where("ccode",$data['ccode']);
    $sql->orderBy('created_at', 'ASC');
    $object = $sql->get();
     
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
	
      
}