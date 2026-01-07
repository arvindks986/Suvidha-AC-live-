<?php 
namespace App\models\Admin\mparty;
use Illuminate\Database\Eloquent\Model;
use DB;
class MPartyModel extends Model
{
  protected $table = 'm_party';
  public $fillable = ['CCODE','PARTYABBRE','PARTYHABBR','PARTYNAME','PARTYHNAME',
        'PARTYTYPE','PARTYSYM','PARTYHFOCABBR','PARTYHFOCNAME','deleteflag','party_reg_date',
        'party_typeid','remarks','created_at','added_created_at','updated_at',
        'added_updated_at','created_by','updated_by'];

public static function add_mparty($data = array()){
   // dd($data);
    if(!empty($data['ccode']) && isset($data['ccode'])){
      $object = MPartyModel::where('CCODE',$data['ccode'])->first();
    
      $object->PARTYABBRE = $data['partyabbre']; 
      $object->PARTYHABBR = $data['partyhabbr']; 
      $object->PARTYNAME = $data['partyname']; 
      $object->PARTYHNAME = $data['partyhname']; 
      $object->PARTYTYPE = $data['partytype'];
      $object->party_reg_date = $data['party_reg_date']; 
      $object->deleteflag ='N';  
      $object->PARTYSYM ='0'; 
      $object->party_typeid = $data['party_typeid'];
      $object->remarks = $data['remarks'];
     // $object->updated_at = date('Y-m-d H:i:s');
      $object->added_update_at =date('Y-m-d');
      $object->updated_by =\Auth::user()->officername;
      MPartyModel::where('CCODE',$data['ccode'])->update($object);
     //dd($object );
     }else{
      $object = new MPartyModel();
      $object->PARTYABBRE = $data['partyabbre']; 
      $object->PARTYHABBR = $data['partyhabbr']; 
      $object->PARTYNAME = $data['partyname']; 
      $object->PARTYHNAME = $data['partyhname']; 
      $object->PARTYTYPE = $data['partytype']; 
      $object->party_reg_date = $data['party_reg_date']; 
      $object->deleteflag ='N'; 
      $object->party_typeid = $data['party_typeid'];
      $object->remarks = $data['remarks'];
      $object->created_at = date('Y-m-d H:i:s');
      $object->added_created_at =date('Y-m-d');
      $object->created_by =\Auth::user()->officername;
    }
 
    if(!$object->save()){
      return false;
    }
     
    return true;
  }

  public static function get_byccode($ccode){
    $object = MPartyModel::where('CCODE',$ccode)->first();
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
  public static function get_allpartie($data = array()){  
    if($data['party_type']=='') {
    //$sql->leftjoin('m_state','m_state.ST_CODE','=','d_party.ST_CODE');

    $object = MPartyModel::leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','m_party.PARTYSYM')
        ->where('deleteflag','N')
        ->where(function($query){
                  $query->where('PARTYTYPE','N')
                        ->orWhere('PARTYTYPE','S')
                        ->orWhere('PARTYTYPE','U');
                     })
    		 
        
    		->orderByRaw('m_party.PARTYABBRE')
        ->selectRaw("m_party.*,m_symbol.SYMBOL_DES,m_symbol.SYMBOL_HDES,m_symbol.SYMBOL_BMP,m_symbol.SYMBOL_HFOCDES,m_symbol.Ind_Symbol,
          m_symbol.Symbol_Img,m_symbol.CONTENT_TYPE")
    		->get();
    } 
    else {
      $object = MPartyModel::leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','m_party.PARTYSYM')
        ->where('deleteflag','N')
        ->where('PARTYTYPE',$data['party_type']) 
        ->orderByRaw('m_party.PARTYABBRE')
        ->selectRaw("m_party.*,m_symbol.SYMBOL_DES,m_symbol.SYMBOL_HDES,m_symbol.SYMBOL_BMP,m_symbol.SYMBOL_HFOCDES,m_symbol.Ind_Symbol,
          m_symbol.Symbol_Img,m_symbol.CONTENT_TYPE")
        ->get();
    }
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
  public static function get_dparties($data = array()){
    $sql = MPartyModel::leftjoin('d_party','d_party.PARTYABBRE','=','m_party.PARTYABBRE');
    $sql->where('m_party.deleteflag','N');
    $sql->where('m_party.PARTYTYPE','S');
    $sql->selectRaw("m_party.CCODE,m_party.PARTYABBRE, m_party.PARTYNAME");
    $query=$sql->groupby('m_party.PARTYABBRE');
    $query=$sql->orderByRaw('m_party.PARTYABBRE')->get();
   // dd($query);
    if(!$query){
      return false;
    }
      return $query->toArray();
       
  }
   
	public static function get_parties($data = array()){
		$sql = MPartyModel::leftjoin('d_party','d_party.PARTYABBRE','=','m_party.PARTYABBRE');
    $sql->leftjoin('m_state','m_state.ST_CODE','=','d_party.ST_CODE');
		$sql->where('deleteflag','N');
    $sql->where('m_party.PARTYTYPE',$data['party_type']);
    $sql->where('d_party.deleted','0');
		 
	  $sql->selectRaw("m_party.CCODE,m_party.PARTYABBRE,m_party.PARTYHABBR,m_party.PARTYNAME, m_party.PARTYHNAME, 
          m_party.PARTYTYPE,m_party.PARTYSYM, d_party.id, d_party.ST_CODE,m_state.ST_NAME,d_party.added_update_at, 
          d_party.remarks,d_party.created_at,d_party.added_created_at,d_party.updated_at,d_party.created_by,d_party.updated_by,d_party.deleted");
		$query=$sql->orderByRaw('m_party.PARTYABBRE')->get();
   // dd($query);
    if(!$query){
      return false;
    }
      return $query->toArray();
    	 
	}
  
	 
    public static function getpartybyabbre($abbre){
            $object = MPartyModel::where('PARTYABBRE',$abbre)->first();

            if(!$object){
              return false;
            }
            return $object->toArray();
          }
public static function getpartybytype($type){
            $object = MPartyModel::select('CCODE','PARTYNAME')->where('PARTYTYPE',$type)->get();

            if(!$object){
              return false;
            }
            return $object->toArray();
          }

    public static function get_partiesbypartyabbre($data = array()){
            $sql = MPartyModel::leftjoin('d_party','d_party.PARTYABBRE','=','m_party.PARTYABBRE');
            $sql->leftjoin('m_state','m_state.ST_CODE','=','d_party.ST_CODE');
            $sql->where('m_party.PARTYABBRE',$data['partyabbre']);
            $sql->where('d_party.deleted','0'); 
            $sql->selectRaw("m_party.CCODE,m_party.PARTYABBRE,m_party.PARTYNAME,m_party.PARTYTYPE, d_party.id,
                d_party.ST_CODE,m_state.ST_NAME,d_party.added_update_at,d_party.remarks ");
            $query=$sql->orderByRaw('d_party.id')->get();
           // dd($query);
            if(!$query){
              return false;
            }
              return $query->toArray();
               
          }
    public static function partyassignsymbol($data = array()){
          $party = [];
          $sql = MPartyModel::leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','m_party.PARTYSYM');
          $sql->where('m_party.deleteflag','N');
          if($data['party_type']==''){
              $sql->where(function ($sql) {
                  $sql->where('PARTYTYPE', '=', 'N')
                      ->orwhere('PARTYTYPE', '=', 'S')->orwhere('PARTYTYPE', '=', 'U');
              });
               
            }
          else{
            $sql->where('m_party.PARTYTYPE',$data['party_type']);
          }
          $sql->where('m_party.PARTYSYM','<>','0');
          $sql->whereNotNull('m_party.PARTYSYM');
          $sql->selectRaw("m_party.CCODE,m_party.PARTYABBRE,m_party.PARTYNAME,m_party.PARTYTYPE,m_party.PARTYSYM, m_symbol.SYMBOL_DES,m_symbol.SYMBOL_HDES,m_symbol.Ind_Symbol,m_symbol.Symbol_Img,m_symbol.CONTENT_TYPE");
          $sql->orderByRaw('m_party.PARTYTYPE','ASC');
          $query=$sql->orderByRaw('m_party.PARTYABBRE','ASC')->get(); 
 
          if(!$query){
            return false;
          }
          return $query->toArray();
        }
    public static function getpartiesnotassignsymbol(){ 
             $sql = MPartyModel::where('deleteflag','N');
             $sql->where(function ($sql) {
                  $sql->where('PARTYSYM', '=', '0')
                      ->orWhereNull('PARTYSYM');
              }
             );
            $query=$sql->orderByRaw('PARTYABBRE','ASC')->get();  
          if(!$query){
            return false;
          }
          return $query->toArray();
        }
    public static function partiesnotassignsymbol(){ 
             $sql = MPartyModel::where('deleteflag','N');
             $sql->where(function ($sql) {
                  $sql->where('PARTYTYPE', '=', 'N')
                      ->orwhere('PARTYTYPE', '=', 'S');
              });
             $sql->where(function ($sql) {
                  $sql->where('PARTYSYM', '=', '0')
                      ->orWhereNull('PARTYSYM');
              });
             
            $query=$sql->orderByRaw('PARTYABBRE','ASC')->get();  
          if(!$query){
            return false;
          }
          return $query->toArray();
        }
    public static function countpartiesbytype($party_type=''){  
          if($party_type=='') {
          $object = MPartyModel::where('deleteflag','N')
              ->where(function($query){
                  $query->where('PARTYTYPE','N')
                        ->orWhere('PARTYTYPE','S')
                        ->orWhere('PARTYTYPE','U')
                        ->orWhere('PARTYTYPE','Z');
                        //->orWhere('PARTYTYPE','Z1');
                     })
                 ->get();
          } 
          else {
          $object = MPartyModel::where('PARTYTYPE',$party_type)->where('deleteflag','N')->get();
          }
          if(!$object){
            return 0;
          }
          return $object->count();
        }
  public static function delisting_party($data = array()){  
    if($data['party_type']=='') {
    $object = MPartyModel::leftjoin('m_symbol','m_symbol.SYMBOL_NO','=','m_party.PARTYSYM')
                ->where('deleteflag','Y')
              //->orwhere('PARTYTYPE','S')
              //->orwhere('PARTYTYPE','U')
              //->where('PARTYTYPE','N')
              ->orderByRaw('m_party.PARTYABBRE')->get();
    } 
    else {
      $object = MPartyModel::where('PARTYTYPE',$data['party_type'])
                ->where('deleteflag','Y') 
                ->orderByRaw('m_party.PARTYABBRE')->get();
    }
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
  public static function delisting_report($data = array()){  
    $object = MPartyModel::select('CCODE','PARTYABBRE','PARTYHABBR','PARTYNAME','PARTYHNAME','PARTYTYPE')
        ->where('deleteflag','Y')
        ->orderByRaw('m_party.PARTYABBRE')
        ->get();
     
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
}