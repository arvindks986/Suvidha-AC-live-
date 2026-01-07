<?php 
namespace App\models\Admin\mparty;
use Illuminate\Database\Eloquent\Model;
use DB;
class SymbolModel extends Model
{
  protected $primaryKey = 'SYMBOL_NO';
  public $timestamps = false;
  protected $table = 'm_symbol';
  public $fillable = ['SYMBOL_NO','SYMBOL_DES','SYMBOL_HDES','SYMBOL_BMP',
        'SYMBOL_HFOCDES','Ind_Symbol','Symbol_Img','CONTENT_TYPE','remarks',
        'created_at','added_created_at','updated_at','added_updated_at',
        'created_by','updated_by'];

public static function add_symbol($data = array()){ //dd($data);
    
      $object = new SymbolModel();
      $object->SYMBOL_DES = $data['symbol_des']; 
      $object->SYMBOL_HDES = $data['symbol_hdes']; 
      $object->Ind_Symbol = $data['ind_symbol']; 
      $object->Symbol_Img = $data['symbol_img']; 
      $object->CONTENT_TYPE = $data['content_type']; 
      $object->remarks = $data['remarks'];
      $object->created_at = date('Y-m-d H:i:s');
      $object->added_created_at =date('Y-m-d');
      $object->created_by =\Auth::user()->officername;
    
      $object->save();
      return true;
  }

  public static function get_bysysno($sysno){
    $object = SymbolModel::where('SYMBOL_NO',$sysno)->first();
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
  public static function get_allsymbol($data = array()){
    //dd($data);
    $sql_raw = "SYMBOL_NO,SYMBOL_DES, SYMBOL_HDES,SYMBOL_BMP,SYMBOL_HFOCDES,Ind_Symbol,Symbol_Img,CONTENT_TYPE,
              remarks,created_at,added_created_at,updated_at,added_updated_at,created_by,updated_by";
    
    $sql = SymbolModel::selectRaw($sql_raw);

    if(!empty($data['freesymbol'])){
           if($data['freesymbol']=="PARTY")
                 $sql->whereNull("Ind_Symbol");
          else
            $sql->where("Ind_Symbol",  $data['freesymbol']);
    }

    if(!empty($data['symbol_img'])){
           if($data['symbol_img']=="NOT")
                 $sql->whereNull("Symbol_Img");
          elseif($data['symbol_img']=="T")
            $sql->where("Symbol_Img", '<>', '');
    }
    $sql->orderBy('SYMBOL_DES', 'ASC');

    $object = $sql->get();
     
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
	
  public static function get_allfreesymbol($data = array()){
    //dd($data);
    $sql_raw = "SYMBOL_NO,SYMBOL_DES, SYMBOL_HDES,SYMBOL_BMP,SYMBOL_HFOCDES,Ind_Symbol,Symbol_Img,CONTENT_TYPE,
              remarks,created_at,added_created_at,updated_at,added_updated_at,created_by,updated_by";
    
    $sql = SymbolModel::selectRaw($sql_raw);
    
    $sql->whereRaw("m_symbol.SYMBOL_NO NOT IN ( SELECT m_symbol.SYMBOL_NO FROM m_symbol JOIN m_party ON m_symbol.SYMBOL_NO=m_party.PARTYSYM)");

    if(!empty($data['symbol_img'])){
           if($data['symbol_img']=="NOT")
                 $sql->whereNull("Symbol_Img");
          elseif($data['symbol_img']=="T")
            $sql->where("Symbol_Img", '<>', '');
    }
     if(!empty($data['freesymbol'])){
           if($data['freesymbol']=="PARTY")
                 $sql->whereNull("Ind_Symbol");
          else
            $sql->where("Ind_Symbol",  $data['freesymbol']);
    }
    $sql->orderBy('SYMBOL_DES', 'ASC');

    $object = $sql->get();
     
    if(!$object){
      return false;
    }
    return $object->toArray();
  } 
  
  public static function countfreesymbol($data = array()){
     $sql_raw = "*";
     $sql = SymbolModel::selectRaw($sql_raw);
     $sql->whereRaw("m_symbol.SYMBOL_NO NOT IN ( SELECT m_symbol.SYMBOL_NO FROM m_symbol JOIN m_party ON m_symbol.SYMBOL_NO=m_party.PARTYSYM)");

    if(!empty($data['symbol_img'])){
           if($data['symbol_img']=="NOT")
                 $sql->whereNull("Symbol_Img");
          elseif($data['symbol_img']=="T")
            $sql->where("Symbol_Img", '<>', '');
    }
     if(!empty($data['freesymbol'])){
           if($data['freesymbol']=="PARTY")
                 $sql->whereNull("Ind_Symbol");
          else
            $sql->where("Ind_Symbol",  $data['freesymbol']);
    }
     $object = $sql->get();
     
    if(!$object){
      return 0;
    }
    return $object->count();
  }    

  public static function symbolallotedtoparty(){
    $record = DB::table('m_symbol')
            ->join('m_party', 'm_party.PARTYSYM','=', 'm_symbol.SYMBOL_NO')
            ->groupby('m_symbol.SYMBOL_NO')->get()->count();
      
    return $record;
  } 

  public static function reportsymbol($data = array()){
    $sql_raw = "SYMBOL_NO,SYMBOL_DES, SYMBOL_HDES,Ind_Symbol,Symbol_Img,created_at";
    $sql = SymbolModel::selectRaw($sql_raw);
    if(!empty($data['freesymbol'])){
           if($data['freesymbol']=="PARTY")
                 $sql->whereNull("Ind_Symbol");
          else
            $sql->where("Ind_Symbol",  $data['freesymbol']);
    }
    $sql->orderBy('SYMBOL_DES', 'ASC');

    $object = $sql->get();
     
    if(!$object){
      return false;
    }
    return $object->toArray();
  }   
}