<?php 
namespace App\models\Admin;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Auth;
use App\models\Admin\mparty\SymbolModel;
class StateSymbolModel extends Model
{
protected $table = 'm_state_symbol';
protected $fillable =['id','st_code','symbol_no','symbol_name','symbol_hname','symbol_vname','ind_symbol','created_by','added_created_at','created_at','updated_at',
'updated_by','transactiontime'];
private $symbol_no = [];


public static function getallsymbolbystate($st_code){
$results = array();
$sql = StateSymbolModel::select("*")->where('st_code', '=', $st_code) ;
$results =  $sql->orderby("symbol_name")->get();

return $results;
} 

public static function getsymbolbysymbolno($st_code, $symbol_no){
   $results = [];
  $sql = StateSymbolModel::selectRaw("symbol_name,symbol_hname,symbol_vname")
          ->where('st_code', '=', $st_code)
          ->where('symbol_no', '=', $symbol_no) ;
   $results =  $sql->first();
  
  return $results;
}

public static function insert_symbol_record($st_code){

date_default_timezone_set('Asia/Kolkata');
$datetime = date("Y-m-d H:i:s");
$table = "candidate_nomination_detail";

$data = DB::table($table)->select('symbol_id')->where('st_code',$st_code)->orderby("symbol_id")->groupby('symbol_id')->get();

if($data){
  $results = [];
  foreach ($data as   $value) {
   
   $party_rec = DB::table('m_symbol')
              ->select('SYMBOL_NO','SYMBOL_DES','SYMBOL_HDES','Ind_Symbol')
              ->where('SYMBOL_NO',$value->symbol_id)->first();
    $rec = DB::table('m_state_symbol')
                          ->select('symbol_no', 'symbol_name', 'symbol_hname', 'symbol_vname')
                          ->where('symbol_no',$value->symbol_id)->first();
      
     
         if(!isset($rec)) { 
         if(isset($party_rec)) {
          $update_record = [
              'st_code'               => $st_code,
              'symbol_no'              =>$party_rec->SYMBOL_NO,
              'symbol_name'           => $party_rec->SYMBOL_DES,
              'symbol_hname'          => $party_rec->SYMBOL_HDES,
              'ind_symbol'            => $party_rec->Ind_Symbol,
              'added_created_at'      => date("Y-m-d"),
              'created_at'            => $datetime,
              'created_by'            => Auth::user()->officername,
          ];
          
           StateSymbolModel::insert($update_record);
       }

        }
  else {
      if(isset($party_rec)) {
              $update_record = [
                      'st_code'               => $st_code,
                      'symbol_no'             => $party_rec->SYMBOL_NO,
                      'symbol_name'           => $party_rec->SYMBOL_DES,
                      'symbol_hname'          => $party_rec->SYMBOL_HDES,
                      'ind_symbol'            => $party_rec->Ind_Symbol, 
                      'updated_at'            => $datetime,
                      'updated_by'            => Auth::user()->officername,
                  ];
                StateSymbolModel::where('symbol_no',$value->symbol_id)->update($update_record);
            }
      }
  }

}

} // end function
public static function getbyid($id){
   $results = [];
  $sql = StateSymbolModel::selectRaw("symbol_name,symbol_hname,symbol_vname")->where('id', '=', $id);
   $results =  $sql->first();
  
  return $results;
}
public static function insert_symbol($st_code){
date_default_timezone_set('Asia/Kolkata');
$datetime = date("Y-m-d H:i:s");
$records=SymbolModel::selectRaw("SYMBOL_NO,SYMBOL_DES,SYMBOL_HDES,Ind_Symbol")
->orderby("SYMBOL_NO",'ASC')->get()->toArray();
//dd($records);

if($records){
  foreach ($records as   $value) {
    set_time_limit(0);
    $rec =StateSymbolModel::where('st_code',$st_code)
              ->where('symbol_no',$value['SYMBOL_NO'])->first();
               
 if(!isset($rec)) {  
     $update_record = [
      'st_code'               => $st_code,
      'symbol_no'             => $value['SYMBOL_NO'],
      'symbol_name'           => $value['SYMBOL_DES'],
      'symbol_hname'          => $value['SYMBOL_HDES'],
      'ind_symbol'            => $value['Ind_Symbol'],
      'added_created_at'      => date("Y-m-d"),
      'created_at'            => $datetime,
      'created_by'            => Auth::user()->officername,
  ];
          
  StateSymbolModel::insert($update_record);

        }
  else {
$update_record = [
      'symbol_name'           => $value['SYMBOL_DES'],
      'symbol_hname'          => $value['SYMBOL_HDES'],
      'ind_symbol'            => $value['Ind_Symbol'],
      'updated_at'            => $datetime,
      'updated_by'            => Auth::user()->officername,
   ];
   //dd($update_record);
  StateSymbolModel::where('symbol_no',$value['SYMBOL_NO'])
  ->where('st_code',$st_code)->update($update_record);
}
}

}
} // end function
}
