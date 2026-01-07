<?php 
 namespace App\models\Admin;
 use Illuminate\Database\Eloquent\Model;
 use DB;
 use Illuminate\Support\Facades\Auth;
 use App\models\Admin\mparty\MPartyModel;
class StatepartyModel extends Model
{
    protected $table = 'm_state_party';
    protected $fillable = ['id', 'st_code', 'party_id', 'party_abbre',
      'party_habbre','party_name','party_hname','party_vname','created_by',
    'added_created_at','created_at','updated_at','updated_by','transactiontime'];
    private $party_id = [];
	
//Jitendra Code 
public static function getallpartybystate($st_code){
        $results = array();
        $sql = StatepartyModel::select('id','st_code','party_id','party_abbre',
            'party_habbre','party_name','party_hname','party_vname')
            ->where('st_code','=',$st_code);
        $results = $sql->orderby("party_name")->get();
	    return $results;
    } 

    public static function getpartybypartyid($st_code, $party_id){
             $results = [];
            $sql = StatepartyModel::selectRaw("party_name,party_hname,party_vname")
                    ->where('st_code', '=', $st_code)
                    ->where('party_id', '=', $party_id) ;
             $results =  $sql->first();
            
            return $results;
    }

   public static function insert_party_record($st_code){

        date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");
        $table = "candidate_nomination_detail";
 
        $data = DB::table($table)->select('party_id')->where('st_code',$st_code)->orderby("party_id")->groupby('party_id')->get();
         
        if($data){
            $results = [];
            foreach ($data as   $value) {
             
             $party_rec = DB::table('m_party')
                        ->select('CCODE', 'PARTYABBRE','PARTYHABBR', 'PARTYNAME', 'PARTYHNAME')
                        ->where('CCODE',$value->party_id)->first();
              $rec = DB::table('m_state_party')
                        ->select('party_id', 'party_abbre','party_name', 'party_hname', 'party_vname')
                        ->where('party_id',$value->party_id)->first();
                
                
                   if(!isset($rec)) {  
                    $update_record = [
                        'st_code'               => $st_code,
                        'party_id'              => $party_rec->CCODE,
                        'party_abbre'           => $party_rec->PARTYABBRE,
                        'party_habbre'          => $party_rec->PARTYHABBR,
                        'party_name'            => $party_rec->PARTYNAME,
                        'party_hname'           => $party_rec->PARTYHNAME,
                        //'party_vname'           =>'', //$party_rec->PARTYHNAME,
                        'added_created_at'      => date("Y-m-d"),
                        'created_at'            => $datetime,
                        'created_by'            => Auth::user()->officername,
                    ];
                    
                     StatepartyModel::insert($update_record);

                  }
            else {
                $update_record = [
                        'st_code'               => $st_code,
                        'party_id'              => $party_rec->CCODE,
                        'party_abbre'           => $party_rec->PARTYABBRE,
                        'party_habbre'          => $party_rec->PARTYHABBR,
                        'party_name'            => $party_rec->PARTYNAME,
                        'party_hname'           => $party_rec->PARTYHNAME,
                         
                        'updated_at'            => $datetime,
                        'updated_by'            => Auth::user()->officername,
                    ];
                  StatepartyModel::where('party_id',$value->party_id)->where('st_code',$st_code)->update($update_record);
                }
            }

        }

    } // end function

     public static function getbyid($id){
             $results = [];
            $sql = StatepartyModel::selectRaw("party_name,party_hname,party_vname")->where('id', '=', $id);
             $results =  $sql->first();
            
            return $results;
    }
    
    public static function insert_party($st_code){
        date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");
        $records=MPartyModel::where('deleteflag','N')->orderby("CCODE", 'ASC')->get()->toArray();
        //dd(count($records));
         
        if($records){
            foreach ($records as   $value) {
              set_time_limit(0);
              $rec =DB::table('m_state_party')->where('st_code',$st_code)
                        ->where('party_id',$value['CCODE'])->first();
           if(!isset($rec)) {  
               $update_record = [
                'st_code'               => $st_code,
                'party_id'              => $value['CCODE'],
                'party_abbre'           => $value['PARTYABBRE'],
                'party_habbre'          => $value['PARTYHABBR'],
                'party_name'            => $value['PARTYNAME'],
                'party_hname'           => $value['PARTYHNAME'],
                'party_type'            => $value['PARTYTYPE'],
                'added_created_at'      => date("Y-m-d"),
                'created_at'            => $datetime,
                'created_by'            => Auth::user()->officername,
            ];
                    
            StatepartyModel::insert($update_record);

                  }
            else {
        $update_record = [
               // 'st_code'               => $st_code,
                'party_abbre'           => $value['PARTYABBRE'],
                'party_habbre'          => $value['PARTYHABBR'],
                'party_name'            => $value['PARTYNAME'],
                'party_hname'           => $value['PARTYHNAME'],
                'party_type'            => $value['PARTYTYPE'],
                'updated_at'            => $datetime,
                'updated_by'            => Auth::user()->officername,
             ];
            StatepartyModel::where('party_id',$value['CCODE'])
                ->where('st_code',$st_code)->update($update_record);
        }
    }

    }
  } // end function
    
    public static function Allpartybystate($data=array()){
          $party = [];
          $sql =StatepartyModel::select('id','st_code','party_id','party_abbre',
            'party_habbre','party_name','party_hname','party_vname');
          
          if(!empty($data['st_code'])){
                $sql->where("st_code", $data['st_code']);
                }

          if(!empty($data['party_type'])){
                $sql->where("party_type", $data['party_type']);
                }
           $sql->orderby("party_type");     
          $results = $sql->orderby("party_name")->get();  //->skip(415)->take(415)

          if(isset($results) and ($results))
              return $results; 
          else
             return false;

    } // end function
}
