<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
     use Illuminate\Support\Facades\Auth;
class ACROModel extends Model
{
    //
    public function Allcandidatelist($user,$status="all",$search='')
    		{  
    			DB::enableQueryLog();
    		if($user->CONST_TYPE=="AC") { 
    					$v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
    					}
  			elseif($user->CONST_TYPE=="PC") { 
  						$v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
  					}
       
  			$a='4'; $a1='3';$a2='5';$a3='6';$a4='2';$a5='1'; 
  			if($status=="all" || $status=="") {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
				 		->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m)
            ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		    		->where(function($query1) use ($a,$a1,$a2,$a3,$a4,$a5){
                    		$query1->where('candidate_nomination_detail.application_status','=',$a)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a1)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a2)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a3)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a4)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a5);
              			})
          ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
           ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC')  
    			  ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();         
    		   }
    		   else { //dd("hello");
    		   		$list = DB::table('candidate_nomination_detail')
		   			   ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
		    	     ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
                ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
               ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		    	     ->where('candidate_nomination_detail.application_status','=',$status)
               ->Where('candidate_personal_detail.cand_name', 'like', '%' .$search. '%')
		    	     ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC') 
                ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();    
    		   }	
    		       // $query = DB::getQueryLog();
					   
					  //  dd($query);
    		   return $list;
    		}
    public function withdrawn($user,$status="all",$search='')
        {  
       
        if($user->CONST_TYPE=="AC") { 
              $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
              }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $a='5'; $a1='6'; 
        if($status=="all" || $status=="") {
          $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m)
             ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            //->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where(function($query1) use ($a,$a1){
                        $query1->where('candidate_nomination_detail.application_status','=',$a)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a1);
                    })
          ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
          ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC') 
          ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();     
           }
           else { //dd("hello");
              $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
          ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
           ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
          ->where('candidate_nomination_detail.application_status','=',$status)
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
         // ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
          ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC') 
           ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();  

          
           }  
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
  			$a='1'; $a1='2';$a2='3';$a3='4'; $a4='5';$a5='6'; 
      if($status=="all" || $status=="") {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					  ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
             ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where(function($query1) use ($a,$a1,$a2){
                        $query1->where('candidate_nomination_detail.application_status','=',$a)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a1)
                        ->orWhere('candidate_nomination_detail.application_status','=',$a2);
                    })
            ->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC')
    				  ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();   
    		   }
    		   else {  
    		   		$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					  ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
             ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		    		->where('candidate_nomination_detail.application_status','=',$status)
		    		->orderBy('candidate_nomination_detail.cand_sl_no', 'ASC')
    				  ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();  
    		    }		 
    		   return $list;
    		}
  public function acceptedcandidate($user,$search='')
        {  
        if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
          
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
             ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           //  ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.application_status','=','6')
            //->where('candidate_nomination_detail.symbol_id','<>','200')
            ->where('candidate_nomination_detail.symbol_id','<>','0')
            ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            ->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();    
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
           // ->leftjoin('candidate_affidavit_detail','candidate_nomination_detail.nom_id','=','candidate_affidavit_detail.nom_id')
          ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m)
           ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID) 
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          //->where('candidate_nomination_detail.symbol_id','=','0')
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          //->where('candidate_affidavit_detail.nom_id','=','6')
           ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();   
           
          return $list;
        }
    public function Symbolassign($nom_id)
        {   
         $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
             ->where('candidate_nomination_detail.nom_id','=',$nom_id)
             ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.cand_vname','candidate_personal_detail.cand_alias_name','candidate_personal_detail.candidate_father_name')->first();
           return $list;
        }
   /* public function finalize_candidate_ac($st,$ac,$actype,$dat)
        {
         $udata = array('finalize'=>'1'); 
         DB::table('candidate_finalized_ac')->where('st_code',$st)->where('const_no',$ac)->where('const_type',$actype)->update($dat);
         if($actype=="AC") {$field="ac_no"; $val=$ac; } elseif($actype=="PC"){$field="pc_no"; $val=$ac; }

          DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->update($udata);
          $r =DB::table('candidate_personal_detail')->where('cand_name','NOTA')->first();  
          $tot =DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->where('finalize','1')->where('application_status','6')->get()->count();
          
          if(!isset($r)){
            $candata=array('cand_name'=>'NOTA','cand_hname'=>'इनमे से कोई नहीं ');   
            $c = DB::table('candidate_personal_detail')->insert($candata);
            $r =DB::table('candidate_personal_detail')->where('cand_name','NOTA')->first(); 
          } 
          $checknota =DB::table('candidate_nomination_detail')->where('candidate_id',$r->candidate_id)->where('st_code',$st)->where($field,$val)->first(); 
           $new_sr=$tot+1;
          // dd($checknota);
		  $lis_ac=getacbyacno($st,$ac);
          $r1 =DB::table('candidate_nomination_detail')
                        ->where('st_code',$st)->where($field,$val)
                        ->where('finalize','1')->where('application_status','6')->first();  
          $nom_data = array('candidate_id'=>$r->candidate_id,
                            'ac_no'=>$r1->ac_no,
                            'election_id'=>$r1->election_id,
                            'district_no'=>$lis_ac->DIST_NO_HDQTR,
                            'pc_no'=>$r1->pc_no,
                            'ST_CODE'=>$st,
                            'finalize'=>'1',
                            'application_status'=>'6',
                            'new_srno'=>$new_sr,
                            'date_of_submit'=>date("Y-m-d"),
                            'scrutiny_date'=>date("Y-m-d"),
                            'party_id'=>'1180',
                            'symbol_id'=>'-1',
                            'finalaccepted'=>'1',
                            'cand_party_type'=>'-Z'); 
          
          if(empty($checknota)) {
              $n = DB::table('candidate_nomination_detail')->insert($nom_data);
            }
          else{  
            $n_data = array('finalize'=>'1','new_srno'=>$new_sr,'finalaccepted'=>1); 
            //echo $checknota->nom_id;
            //dd($n_data);
             DB::table('candidate_nomination_detail')->where('nom_id',$checknota->nom_id)->where($field,$val)->update($n_data);
          }
          return true;
        } */


        public function finalize_candidate_ac($st,$ac,$actype,$dat)
        {
          DB::beginTransaction();
          try{
         $udata = array('finalize'=>'1'); 
         $updtindex=DB::table('candidate_finalized_ac')->where('st_code',$st)->where('const_no',$ac)->where('const_type','AC')->update($dat);
         if($actype=="AC") {$field="ac_no"; $val=$ac; } elseif($actype=="PC"){$field="pc_no"; $val=$ac; }

          DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->update($udata);
          $r =DB::table('candidate_personal_detail')->where('cand_name','NOTA')->first();  
          $tot =DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->where('finalize','1')->where('application_status','6')->where('party_id','!=',1180)->get()->count();
         
          if(!isset($r)){
            $candata=array('cand_name'=>'NOTA','cand_hname'=>'इनमे से कोई नहीं ');   
            $c = DB::table('candidate_personal_detail')->insert($candata);
            $r =DB::table('candidate_personal_detail')->where('cand_name','NOTA')->first(); 
          }


          $checknota =DB::table('candidate_nomination_detail')->where('candidate_id',$r->candidate_id)->where('st_code',$st)->where('ac_no',$ac)->first(); 

           $new_sr=$tot+1;
          
      $lis_ac=getacbyacno($st,$ac);
          $r1 =DB::table('candidate_nomination_detail')
                        ->where('st_code',$st)->where($field,$val)->where('finalaccepted','1')
                        ->where('finalize','1')->where('application_status','6')->first();  

          $nom_data = array('candidate_id'=>$r->candidate_id,
                            'ac_no'=>$r1->ac_no,
                            'election_id'=>$r1->election_id,
                            'district_no'=>$lis_ac->DIST_NO_HDQTR,
                            'pc_no'=>$r1->pc_no,
                            'ST_CODE'=>$st,
                            'finalize'=>'1',
                            'application_status'=>'6',
                            'new_srno'=>$new_sr,
                            'date_of_submit'=>date("Y-m-d"),
                            'scrutiny_date'=>date("Y-m-d"),
                            'party_id'=>'1180',
                            'symbol_id'=>'-1',
                            'finalaccepted'=>'1',
                           // 'election_type_id'=>$r1->election_type_id,
                            'cand_party_type'=>'-Z'); 
          
          if(isset($checknota) && !empty($checknota)) {
            $n_data = array('finalize'=>'1','new_srno'=>$new_sr,'finalaccepted'=>1); 
            
             DB::table('candidate_nomination_detail')->where('nom_id',$checknota->nom_id)->where($field,$val)->update($n_data);
             
            }
          else{  

             $n = DB::table('candidate_nomination_detail')->insert($nom_data);

           
          }
          DB::commit();
        }catch (Exception $e) {
               DB::rollback();
              // return $e->getMessage();

             }
          return true;
        }


     public function public_affidavit_ac($st,$ac)
        {
         $udata = array('affidavit_public'=>'yes'); 
          DB::table('candidate_nomination_detail')->where('ST_CODE',$st)->where('ac_no',$ac)->update($udata);
          return true;
        }
    public function checkfinalize_acbyro($st,$ac,$actype='')
        { 
          $rec =DB::table('candidate_nomination_detail')
                    ->where('ST_CODE',$st)->where('ac_no',$ac)
                    ->where('finalize','1')->first();
         if(isset($rec))
                return 1;
          else
                 return 0;
        }
  function getNominationbyId($nomid){
              $getnomination = DB::table('candidate_nomination_detail')->where('nom_id',$nomid)->get();
              return $getnomination ;
      }
  function getcandNomination($candId){
        $getcandNomination = DB::table('candidate_nomination_detail')->where('candidate_id',$candId)->get();
        return $getcandNomination ;
    }
      
    
    function getCandidateByOfficerId($officerName)
            {
            $getCandidateByOfficerId = DB::table('candidate_personal_detail')->where('created_by', $officerName)->get();
            return $getCandidateByOfficerId;
            }
    function getallaffidavitbyro($officerId)
            {
            $getaffidavitbyOfficerId = DB::table('candidate_affidavit_detail')->where('created_by', $officerId)->get();
             return ($getaffidavitbyOfficerId);
            }
    function getcounteraffidavitbyOfficerId($officerId){
        //DB::enableQueryLog();
        $getcounteraffidavitbyOfficerId = DB::table('candidate_counteraffidavit_detail')->where('created_by', $officerId)->get();
        //dd(DB::getQueryLog());
       return ($getcounteraffidavitbyOfficerId);
    }
    function getaffidavit($candId,$nomid){
        //DB::enableQueryLog();
        $getaffidavit = DB::table('candidate_affidavit_detail')->where('candidate_id', $candId)->where('nom_id', $nomid)->first();
        //dd(DB::getQueryLog());
       return ($getaffidavit);
    } 
  public function contestingcandidate($user,$search='')
        {  
        if($user->CONST_TYPE=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
                        }
        elseif($user->CONST_TYPE=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$user->CONST_NO; 
            }
        $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m)
             ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            ->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
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
            'candidate_nomination_detail.cand_party_type','candidate_personal_detail.cand_category','candidate_personal_detail.cand_alias_vname')->get();  
          return $list;
        }  

  public function candidatelist($st_code,$const_no)
        {  
           $list = DB::table('candidate_nomination_detail')
                ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                ->where('candidate_nomination_detail.st_code','=',$st_code) 
                ->where('candidate_nomination_detail.ac_no','=',$const_no)
                 ->where('candidate_nomination_detail.election_id','=',Auth::user()->election_id)
                ->where('candidate_nomination_detail.application_status','<>','11')
                ->groupby('candidate_personal_detail.candidate_id') 
                ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_mobile','candidate_personal_detail.candidate_residence_acno','candidate_personal_detail.candidate_father_name','candidate_personal_detail.candidate_id')->get(); 
         
          return $list;
        }
    public function form3areportsdetails($user,$sub_date)
              {  
                 
               if($user['CONST_TYPE']=="AC") { 
                    $v= 'candidate_nomination_detail.ac_no'; $m=$user['CONST_NO']; 
                  }
              $list = DB::table('candidate_nomination_detail')
                  ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
                  ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
                  ->where('candidate_nomination_detail.st_code','=',$user['ST_CODE'])->where($v,'=',$m) 
                  ->Where('candidate_nomination_detail.election_id', '=', $user['ELECTION_ID'])
                  ->Where('candidate_nomination_detail.date_of_submit', '=', $sub_date)
                  ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                  ->orderBy('candidate_nomination_detail.new_srno', 'asc')
                  ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name',
                    'candidate_personal_detail.cand_epic_no','candidate_nomination_detail.date_of_submit',
                    'candidate_personal_detail.cand_fhname','candidate_personal_detail.candidate_residence_address',
                    'candidate_personal_detail.candidate_residence_address',
                    'candidate_personal_detail.candidate_residence_stcode',
                    'candidate_personal_detail.candidate_residence_districtno',
                    'candidate_personal_detail.candidate_residence_acno',
                    'candidate_personal_detail.cand_age','candidate_personal_detail.cand_category',
                    'candidate_personal_detail.cand_cast','candidate_personal_detail.candidate_residence_addressv',
                    'candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
                    'candidate_nomination_detail.st_code','candidate_personal_detail.cand_vname',
                    
                    'candidate_nomination_detail.pc_no','candidate_nomination_detail.district_no',
                    'candidate_nomination_detail.cand_sl_no','candidate_personal_detail.cand_fhname',
                    'candidate_nomination_detail.new_srno', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE')
                  ->get(); 

                return $list;
              }   

        public function form4reportsdetails($user,$a,$a1)
              {  
              if($user['CONST_TYPE']=="AC") { 
                    $v= 'candidate_nomination_detail.ac_no'; $m=$user['CONST_NO']; 
                  }
              $list = DB::table('candidate_nomination_detail')
                  ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
                  ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
                  ->where('candidate_nomination_detail.st_code','=',$user['ST_CODE'])->where($v,'=',$m) 
                  ->Where('candidate_nomination_detail.election_id', '=', $user['ELECTION_ID'])
                  ->where('candidate_nomination_detail.application_status','=','6')
                  ->where('candidate_nomination_detail.finalaccepted','=','1')
                  ->Where('candidate_nomination_detail.symbol_id', '<>', '1180')
                  ->where('candidate_nomination_detail.application_status','<>','11')
                  ->where(function($query1) use ($a,$a1){
                         $query1->where('candidate_nomination_detail.cand_party_type','=',$a)
                        ->orWhere('candidate_nomination_detail.cand_party_type','=',$a1);
                    }) 
                  ->orderBy('candidate_nomination_detail.new_srno', 'asc')
                  ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name',
                    'candidate_personal_detail.cand_epic_no','candidate_nomination_detail.date_of_submit',
                    'candidate_personal_detail.cand_fhname','candidate_personal_detail.candidate_residence_address',
                    'candidate_personal_detail.candidate_residence_address',
                    'candidate_personal_detail.candidate_residence_stcode',
                    'candidate_personal_detail.candidate_residence_districtno',
                    'candidate_personal_detail.candidate_residence_acno',
                    'candidate_personal_detail.cand_age','candidate_personal_detail.cand_category',
                    'candidate_personal_detail.cand_cast','candidate_personal_detail.candidate_residence_addressv',
                    'candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
                    'candidate_nomination_detail.st_code','candidate_personal_detail.cand_vname',
                    
                    'candidate_nomination_detail.pc_no','candidate_nomination_detail.district_no',
                    'candidate_nomination_detail.cand_sl_no','candidate_personal_detail.cand_fhname',
                    'candidate_nomination_detail.new_srno', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE')
                  ->get(); 

                return $list;
              }
    public function update_newsequence($user){  
        $lists = DB::table('candidate_nomination_detail')
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)
            ->where('candidate_nomination_detail.ac_no','=',$user->CONST_NO)
            ->where('candidate_nomination_detail.election_id','=',$user->ELECTION_ID)
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_nomination_detail.party_id', '<>', '1180')
            ->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('nom_id','candidate_id','finalaccepted','new_srno','party_id','symbol_id','election_id','ac_no')
             ->get(); 
             if(isset($lists)) {$i=0;
                  foreach ($lists as $list) { $i++;
                    $udata = array('new_srno'=>$i); 
                    DB::table('candidate_nomination_detail')
                      ->where('st_code','=',$user->ST_CODE)
                      ->where('ac_no','=',$user->CONST_NO)->where('election_id','=',$user->ELECTION_ID)
                      ->where('nom_id','=',$list->nom_id)->update($udata);
                  }
              } // end if
        }  



     


  public function partywisecontenestingcandidate4($user,$a,$a1)
        {  
       
        if($user->CONST_TYPE=="AC") { 
              $v= 'candidate_nomination_detail.ac_no'; $m=$user->CONST_NO; 
            }
        $cands = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
        
            ->where('candidate_nomination_detail.st_code','=',$user->ST_CODE)->where($v,'=',$m) 
            ->Where('candidate_nomination_detail.election_id', '=', $user->ELECTION_ID)
            ->where('candidate_nomination_detail.application_status','=','6')
            ->Where('candidate_nomination_detail.party_id', '<>', '1180')
           // ->where('candidate_nomination_detail.finalaccepted','=','1')
            //->where('candidate_nomination_detail.symbol_id','<>','200')
       
         ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where(function($query1) use ($a,$a1){
                        $query1->where('candidate_nomination_detail.cand_party_type','=',$a)
                        ->orWhere('candidate_nomination_detail.cand_party_type','=',$a1);
                    })
            ->groupBy('candidate_nomination_detail.candidate_id')
             //->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
           ->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address',
                 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_nomination_detail.new_srno',
             'candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode',
             'candidate_personal_detail.cand_vname','candidate_personal_detail.candidate_residence_addressv',
                'candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.cand_image',
             'candidate_personal_detail.candidate_residence_acno','candidate_personal_detail.candidate_father_name')->get(); 
           //dd($cands);
  
          return $cands;
        }  
















}
