<?php 
namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class BallotModel extends Model
{
     
	public function etpbscandidate($user)
        { 
         if($user['const_type']=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user['ac_no']; 
                        } 
         $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
           ->leftjoin('m_state_party', 'candidate_nomination_detail.party_id', '=', 'm_state_party.party_id')   
           
           ->where('candidate_nomination_detail.st_code','=',$user['st_code'])->where($v,'=',$m)
            ->Where('candidate_nomination_detail.election_id', '=', $user['election_id']) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            //->where('candidate_nomination_detail.symbol_id','<>','200')
            ->orderBy('candidate_nomination_detail.new_srno', 'asc')   
            ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_vname','m_state_party.party_id',
               'm_state_party.party_name','m_state_party.party_abbre','m_state_party.party_hname','m_state_party.party_vname',
               'candidate_nomination_detail.new_srno','candidate_personal_detail.cand_image')->get(); 
          return $list;
        }  
      
    public function evmballots($user)
        { 
         if($user['const_type']=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user['ac_no']; 
                        } 
        $list = DB::table('candidate_nomination_detail')
              ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
              ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')  
              ->where('candidate_nomination_detail.st_code','=',$user['st_code'])->where($v,'=',$m)
              ->Where('candidate_nomination_detail.election_id', '=', $user['election_id']) 
              ->where('candidate_nomination_detail.application_status','=','6')
              ->where('candidate_nomination_detail.finalaccepted','=','1')
              ->orderBy('candidate_nomination_detail.new_srno', 'asc')   
              ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_vname',
                'candidate_personal_detail.cand_hname','candidate_personal_detail.cand_image',
                'candidate_nomination_detail.new_srno','candidate_nomination_detail.st_code','candidate_nomination_detail.party_id',
                'candidate_nomination_detail.ac_no', 'm_symbol.CONTENT_TYPE',
                'm_symbol.SYMBOL_DES','m_symbol.SYMBOL_HDES','m_symbol.Symbol_Img')->get();  
          return $list;
        }    
}
