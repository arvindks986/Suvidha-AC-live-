<?php
    namespace App\models\Admin;
    use Illuminate\Database\Eloquent\Model;
    use DB;
     use Illuminate\Support\Facades\Auth;
class ApplicantsModel extends Model
{
     
    public function Allapplicantlist($user,$status="all")
        {   
         
        if($user->CONST_TYPE=="AC") { 
                    $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                    }
            elseif($user->CONST_TYPE=="PC") { 
                    $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
                    }
         
        $a='4'; $a1='3';$a2='5';$a3='6';$a4='2';$a5='1'; 
        if($status=="all" || $status=="") {
          $list = DB::table('candidate_nomination_detail')
            ->join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m)
            ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
            ->Where('candidate_nomination_detail.party_id', '<>', '1180')
           // ->Where('candidate_nomination_detail.nomination_type', '<>', '0')
            ->where(function($query1) use ($a,$a1,$a2,$a3,$a4,$a5){
                        $query1->where('candidate_nomination_detail.application_status','=',$a)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a1)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a2)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a3)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a4)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a5);
                    })
            ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC')  
            ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.qrcode','candidate_nomination_detail.candidate_id','finalaccepted', 'candidate_nomination_detail.nomination_type', 
                    'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
                    'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.pc_no',
                    'candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no',
                    'candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type',
                    'candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.rejection_message',
                    'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
                    'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
                    'candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name',
                    'candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image',
                    'candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno',
                    'candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age',
                    'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_addressv',
                    'candidate_nomination_detail.cand_party_type','m_party.PARTYNAME','m_party.PARTYABBRE')->get();         
                }
           else {  
              $list = DB::table('candidate_nomination_detail')
              ->join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
               ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
                ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
                 ->Where('candidate_nomination_detail.party_id', '<>', '1180')
                 ->Where('candidate_nomination_detail.nomination_type', '<>', '0')
                ->where('candidate_nomination_detail.application_status','=',$status)
                ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC') 
                ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.qrcode','candidate_nomination_detail.candidate_id','finalaccepted','candidate_nomination_detail.nomination_type', 
                          'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
                          'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.pc_no',
                          'candidate_nomination_detail.st_code','candidate_nomination_detail.cand_sl_no',
                          'candidate_nomination_detail.new_srno','candidate_nomination_detail.party_type',
                          'candidate_nomination_detail.scrutiny_date','candidate_nomination_detail.rejection_message',
                          'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
                          'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
                          'candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name',
                          'candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image',
                          'candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno',
                          'candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age',
                          'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_addressv',
                          'candidate_nomination_detail.cand_party_type','m_party.PARTYNAME','m_party.PARTYABBRE')->get();    
           }  
           
           return $list;
        }
    
    public function candidateinformation($nom_id){

                 $shares = DB::table('candidate_nomination_detail')
                          ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id') 
                          ->where('candidate_nomination_detail.nom_id', $nom_id)
                          ->select('candidate_personal_detail.candidate_id','candidate_personal_detail.*', 'candidate_nomination_detail.*')
                          ->first();  
                return $shares;
      }

      public function nominationinformation($nom_id){

                 $shares = DB::table('candidate_nomination_detail')
                          ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id') 
                          ->where('candidate_nomination_detail.nom_id', $nom_id)
                           ->select('candidate_nomination_detail.nom_id', 'candidate_nomination_detail.nomination_no','candidate_nomination_detail.qrcode','candidate_nomination_detail.candidate_id','candidate_nomination_detail.nomination_type', 
                            'candidate_nomination_detail.application_status','candidate_nomination_detail.symbol_id',
                            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no',
                            'candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code',
                            'candidate_nomination_detail.party_id','candidate_nomination_detail.district_no',
                            'candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date',
                            'candidate_nomination_detail.scrutiny_time','candidate_nomination_detail.cand_party_type',
                            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.place',
                            'candidate_nomination_detail.proposer_name','candidate_nomination_detail.proposer_slno',
                            'candidate_nomination_detail.fdate','candidate_nomination_detail.new_srno',
                            'candidate_nomination_detail.nomination_papersrno','candidate_nomination_detail.rosubmit_date',
                            'candidate_nomination_detail.rosubmit_time','candidate_nomination_detail.nomination_submittedby',

                            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
                            'candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name',
                            'candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image',
                            'candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno',
                            'candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age',
                            'candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.cand_fhname',
                          'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.cand_fvname',
                          'candidate_personal_detail.candidate_residence_acno',
                          'candidate_personal_detail.candidate_residence_address')
                           ->first();   
                return $shares;
      }

      public function newnominationinformation($nom_id){

        $shares = DB::table('candidate_nomination_detail')
                 ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id') 
                 ->where('candidate_nomination_detail.nomination_no', $nom_id)
                  ->select('candidate_nomination_detail.nom_id', 'candidate_nomination_detail.new_srno', 'candidate_nomination_detail.rosubmit_time', 'candidate_nomination_detail.nomination_no','candidate_nomination_detail.qrcode','candidate_nomination_detail.candidate_id','candidate_nomination_detail.nomination_type', 
                   'candidate_nomination_detail.application_status','candidate_nomination_detail.symbol_id',
                   'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no',
                   'candidate_nomination_detail.pc_no','candidate_nomination_detail.st_code',
                   'candidate_nomination_detail.party_id','candidate_nomination_detail.district_no',
                   'candidate_nomination_detail.party_type','candidate_nomination_detail.scrutiny_date',
                   'candidate_nomination_detail.scrutiny_time','candidate_nomination_detail.cand_party_type',
                   'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.place',
                   'candidate_nomination_detail.proposer_name','candidate_nomination_detail.proposer_slno',
                   'candidate_nomination_detail.fdate','candidate_nomination_detail.new_srno',
                   'candidate_nomination_detail.nomination_papersrno','candidate_nomination_detail.rosubmit_date',
                   'candidate_nomination_detail.rosubmit_time','candidate_nomination_detail.nomination_submittedby',

                   'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
                   'candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name',
                   'candidate_personal_detail.cand_vname','candidate_personal_detail.cand_image',
                   'candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno',
                   'candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age',
                   'candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.cand_fhname',
                 'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.cand_fvname',
                 'candidate_personal_detail.candidate_residence_acno',
                 'candidate_personal_detail.candidate_residence_address')
                  ->first();   
       return $shares;
}
}
