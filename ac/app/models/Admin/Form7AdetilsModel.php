<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class Form7AdetilsModel extends Model
{
    protected $table = 'candidate_form7a_detail';
	
   

    public static function get_records($filter = array()){
        $sql_raw = "*";
    
        $sql = DB::table('candidate_form7a_detail')->selectRaw($sql_raw);
        
        if(!empty($filter['st_code'])){
            $sql->where("st_code",$filter['st_code']);
        }
        if(!empty($filter['dist_no'])){
            $sql->where("dist_no",$filter['dist_no']);
        }
         if(!empty($filter['ac_no'])){
            $sql->where("ac_no",$filter['ac_no']);
        }
        if(!empty($filter['election_id'])){
            $sql->where("election_id",$filter['election_id']);
        }
            $sql->where("const_type",'AC');
        
         $query = $sql->first();
         return $query;
    }

   public function partywiseallcontenestingcandidate($user)
        {  
        if($user['const_type']=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user['ac_no']; 
                        }
        
        $cands = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        
            ->where('candidate_nomination_detail.st_code','=',$user['st_code'])->where($v,'=',$m)
            ->Where('candidate_nomination_detail.election_id', '=', $user['election_id']) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->orderBy('candidate_nomination_detail.new_srno', 'asc')
           ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address',
                 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_nomination_detail.new_srno',
             'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
             'candidate_personal_detail.cand_vname','candidate_personal_detail.candidate_residence_addressv',
                'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.cand_image',
                'candidate_personal_detail.candidate_residence_acno')->get(); 
          return $cands;
        } 
  
	public function partywisecontenestingcandidate($user,$a,$a1)
        {  
        if($user['const_type']=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user['ac_no']; 
                        }
        $cands = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        
            ->where('candidate_nomination_detail.st_code','=',$user['st_code'])->where($v,'=',$m)
            ->Where('candidate_nomination_detail.election_id', '=', $user['election_id']) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
       
         ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->where(function($query1) use ($a,$a1){
                        $query1->where('candidate_nomination_detail.cand_party_type','=',$a)
                        ->orWhere('candidate_nomination_detail.cand_party_type','=',$a1);
                    })
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')   
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address',
                 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_nomination_detail.new_srno',
             'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
             'candidate_personal_detail.cand_vname','candidate_personal_detail.candidate_residence_addressv',
                'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.cand_image',
             'candidate_personal_detail.candidate_residence_acno')->get(); 
          return $cands;
        }  
      
      public function partywiseallcontenestingcandidatevernacular($user)
        {  
        if($user['const_type']=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user['ac_no']; 
                        }
        
        $cands = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_state_party', 'candidate_nomination_detail.party_id', '=', 'm_state_party.party_id')    
            ->leftjoin('m_state_symbol','candidate_nomination_detail.symbol_id','=','m_state_symbol.symbol_no')
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
           
            ->where('candidate_nomination_detail.st_code','=',$user['st_code'])->where($v,'=',$m)
            ->Where('candidate_nomination_detail.election_id', '=', $user['election_id']) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
			->groupBy('candidate_personal_detail.candidate_id')
            ->orderBy('candidate_nomination_detail.new_srno', 'asc')
           ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address',
               'm_state_party.party_name','m_state_party.party_abbre','m_state_party.party_hname','m_state_party.party_vname','m_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES'
               , 'm_state_symbol.symbol_name','m_state_symbol.symbol_hname','m_state_symbol.symbol_vname',
               'candidate_nomination_detail.new_srno','candidate_personal_detail.cand_image',
              'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
              'candidate_personal_detail.cand_vname','candidate_personal_detail.candidate_residence_addressv',
              'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get(); 
          return $cands;
        } 
  
  public function partywisecontenestingcandidatevernacular($user,$a,$a1)
        {  
        if($user['const_type']=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user['ac_no']; 
                        }
        $cands = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_state_party', 'candidate_nomination_detail.party_id', '=', 'm_state_party.party_id')    
            ->leftjoin('m_state_symbol','candidate_nomination_detail.symbol_id','=','m_state_symbol.symbol_no')
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
         
            ->where('candidate_nomination_detail.st_code','=',$user['st_code'])->where($v,'=',$m)
            ->Where('candidate_nomination_detail.election_id', '=', $user['election_id']) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
       
         ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->where(function($query1) use ($a,$a1){
                         $query1->where('candidate_nomination_detail.cand_party_type','=',$a)
                        ->orWhere('candidate_nomination_detail.cand_party_type','=',$a1);
                    })
		->groupBy('candidate_personal_detail.candidate_id')
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')   
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address',
               'm_state_party.party_name','m_state_party.party_abbre','m_state_party.party_hname','m_state_party.party_vname','m_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES'
               , 'm_state_symbol.symbol_name','m_state_symbol.symbol_hname','m_state_symbol.symbol_vname',
               'candidate_nomination_detail.new_srno','candidate_personal_detail.cand_image',
              'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
              'candidate_personal_detail.cand_vname','candidate_personal_detail.candidate_residence_addressv',
              'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get(); 
          return $cands;
        }  
}
