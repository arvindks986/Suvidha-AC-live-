<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class ROModel extends Model
{
    //
    public function Allcandidatelist($user,$status="all")
    		{  
    			DB::enableQueryLog();
    		if($user->CONST_TYPE=="AC") { 
    						$v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
    						}
  			elseif($user->CONST_TYPE=="PC") { 
  						$v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
  					}
  			$a='4'; $a1='3';$a2='5';$a3='6';
  			if($status=="all" || $status=="") {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
				->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)->where($v,'=',$m) 
		         ->where(function($query1) use ($a,$a1,$a2,$a3){
                    		$query1->where('candidate_nomination_detail.application_status','=',$a)
                   	->orWhere('candidate_nomination_detail.application_status','=',$a1)
                    ->orWhere('candidate_nomination_detail.application_status','=',$a2)
                   	->orWhere('candidate_nomination_detail.application_status','=',$a3);
              	  })
					->orderBy('candidate_nomination_detail.cand_party_type', 'desc') 
    			->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip','candidate_personal_detail.cand_panno','candidate_personal_detail.cand_gender','candidate_personal_detail.cand_age')->get(); 
    		   }
    		   else { //dd("hello");
    		   		$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
		    	->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)->where($v,'=',$m) 
		    	->where('candidate_nomination_detail.application_status','=',$status)
		    	->orderBy('candidate_nomination_detail.cand_party_type', 'desc') 
          ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip')->get();
    		   }	
    		        //$query = DB::getQueryLog();
					     //$query = $query;
					     //dd($query);
    		   return $list;
    		}
    public function Allapplicantlist($user,$status="all")
    		{  dd($user); 
    		DB::enableQueryLog();
    		if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
  			$a='1'; $a1='2';$a2='2';
      if($status=="all" || $status=="") {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					  ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)->where($v,'=',$m) 
            ->where(function($query1) use ($a,$a1,$a2){
                        $query1->where('candidate_nomination_detail.application_status','=',$a)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a1)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a2);
                    })
            ->orderBy('candidate_nomination_detail.cand_party_type', 'desc')
    				->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_image')->get();
    		   }
    		   else {  
    		   		$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					  ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)->where($v,'=',$m) 
		    		->where('candidate_nomination_detail.application_status','=',$status)
		    		->orderBy('candidate_nomination_detail.cand_party_type', 'desc')
    				->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_image')->get();  
    		    }		 
    		   return $list;
    		}
  public function acceptedcandidate($user)
        {  
        if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
          //  ->leftjoin('candidate_affidavit_detail', 'candidate_nomination_detail.nom_id', '=', 'candidate_affidavit_detail.nom_id') 
          //  ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          //  ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
            ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)->where($v,'=',$m) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
            ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_image','candidate_personal_detail.is_candidate_vip')->get();
          return $list;
        }
    public function Symbolcandidate($user)
        {  DB::enableQueryLog();
       if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $nu='';
        $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
          ->where('candidate_nomination_detail.ST_CODE','=',$user->ST_CODE)->where($v,'=',$m) 
          //->where('candidate_nomination_detail.symbol_id','=','0')
          ->where('candidate_nomination_detail.application_status','=','6')
          ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name','candidate_personal_detail.cand_image','m_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES')->get();
           
          return $list;
        }
    public function Symbolassign($nom_id)
        {   
         $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
             ->where('candidate_nomination_detail.nom_id','=',$nom_id)
             ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->first();
           return $list;
        }
    public function finalize_candidate_ac($st,$ac,$actype,$dat)
        {
         $udata = array('finalize'=>'1'); 
         DB::table('candidate_finalized_ac')->where('ST_CODE',$st)->where('CONS_NO',$ac)->where('CONS_TYPE',$actype)->update($dat);
         if($actype=="AC") {$field="ac_no"; $val=$ac; } elseif($actype=="PC"){$field="pc_no"; $val=$ac; }

          DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where($field,$val)->update($udata);
          $r =DB::table('candidate_personal_detail')->where('cand_name','NOTA')->first();  
          $tot =DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where($field,$val)->where('finalize','1')->where('application_status','6')->get()->count();
         // dd($tot);
          $checknota =DB::table('candidate_nomination_detail')->where('candidate_id',$r->candidate_id)->where('ST_CODE',$st)->where($field,$val)->first(); 
           $new_sr=$tot+1;
          $r1 =DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where($field,$val)->where('finalize','1')->where('application_status','6')->first();  
          $nom_data = array('candidate_id'=>$r->candidate_id,'ac_no'=>$r1->ac_no,'election_id'=>$r1->election_id,'pc_no'=>$r1->pc_no,'ST_CODE'=>$st,'finalize'=>'1','application_status'=>'6','new_srno'=>$new_sr,'date_of_submit'=>date("Y-m-d"),'scrutiny_date'=>date("Y-m-d"),'party_id'=>'1180'); 
          if(empty($checknota)) {
              $n = DB::table('candidate_nomination_detail')->insert($nom_data);
            }
          return true;
        }
     public function public_affidavit_ac($st,$ac)
        {
         $udata = array('affidavit_public'=>'yes'); 
          DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where('ac_no',$ac)->update($udata);
          return true;
        }
    public function checkfinalize_acbyro($st,$ac,$actype)
        { 
          DB::enableQueryLog();  //echo $st."-".$ac."-".$actype;
         if($actype=="AC")
          $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where('ac_no',$ac)->where('finalize','1')->first();
        else 
          $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where('pc_no',$ac)->where('finalize','1')->first();
 
        if(isset($rec))
                return 1;
          else
                 return 0;
        }
}
