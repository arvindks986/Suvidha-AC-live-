<?php
    namespace App\models\Counting;
    use Illuminate\Database\Eloquent\Model;
    use DB, Auth;

class PostalCountingModel extends Model
    {
       // ALL Module and logic develop by Sachchidanand
      public function getallacbypcno($st_code,$pc_no)
            {       
            $d = DB::table('m_ac')->where('ST_CODE',$st_code )->where('PC_NO',$pc_no )->get();
            return $d;
            } 
     
      
     public function roundsechudle($data = array())
            { 
            $sql_raw = "id,scheduled_round,st_code,ac_no,rejected_votes,postal_total_votes,finalized_ac,added_update_at,tended_votes,tended,postal";

                $sql = DB::table('round_master');
                $sql->selectRaw($sql_raw);

                if(!empty($data['st_code'])){
                        $sql->where("st_code", $data['st_code']);
                      }

                if(!empty($data['ac_no'])){
                        $sql->where("ac_no", $data['ac_no']);
                      }
                if(!empty($data['election_id'])){
                        $sql->where("election_id", $data['election_id']);
                      }
              $query = $sql->first();
              return $query;
             }
        
    
       
        public function checkallacfinalize($data = array()){
                $sql_raw = "id,finalized_ac";
                $sql = DB::table('counting_finalized_ac');
                $sql->selectRaw($sql_raw);

                if(!empty($data['st_code'])){
                        $sql->where("st_code", $data['st_code']);
                      }

                if(!empty($data['ac_no'])){
                        $sql->where("ac_no", $data['ac_no']);
                      }
                if(!empty($data['election_id'])){
                        $sql->where("election_id", $data['election_id']);
                      }
                  $r = $sql->first();
             if(isset($r))
                        return 1;
                  else
                         return 0;
            } 
         
        public function selectsecondhightvalueofcounting($table,$st_code,$acno,$pcno,$eleid){  
                 
             $result = DB::table($table)
                    ->select([DB::raw('id'),DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')]) 
                    ->where('ac_no',$acno)->where('election_id',$eleid)
                    ->where('party_id','<>','1180')  
                    ->groupBy('id')->groupBy('nom_id')
                    ->groupBy('candidate_id')
                    ->orderBy('total_vote', 'desc')->limit(1)->offset(1)
                    ->first();  
             
                    return $result;
            } 
     public function selectfirsthightvalueofcounting($table,$st_code,$acno,$pcno,$eleid)
            { 
             $result = DB::table($table)
                    ->select([DB::raw('id'),DB::raw('nom_id'),DB::raw('candidate_id'),DB::raw('MAX(total_vote) AS max_total')]) 
                    ->where('AC_NO',$acno)->where('election_id',$eleid)
                    ->where('party_id','<>','1180') 
                    ->groupBy('id')->groupBy('nom_id')
                    ->groupBy('candidate_id')->orderBy('total_vote', 'desc')
                    ->first(); 
               return $result;
            } 
        public function cantestesting_nomination($st_code,$ac_no,$elec_id)
                {
                 $ndata=array('symbol_id'=>'-1');
                  $g = DB::table('candidate_nomination_detail')->where('party_id','1180')->update($ndata);
         
                  $result = DB::table('candidate_nomination_detail')
                      ->where('st_code',$st_code)
                      ->where('ac_no',$ac_no)
                      ->where('election_id',$elec_id)
                      ->where('application_status','6')
                      ->where('finalize','1')
                      ->where('finalaccepted','=','1')
                      ->where('symbol_id','<>','200')->orderBy('new_srno','ASC')->get();
                  
                   return $result;  
                } 
        public static function grandtotalsum($table,$round,$data = array()){ 
                     $sql_raw = "id, SUM(IFNULL(round1,0)+IFNULL(round2,0)+IFNULL(round3,0)+
                                IFNULL(round4,0)+IFNULL(round5,0)+IFNULL(round6,0)+
                                IFNULL(round7,0)+IFNULL(round8,0)+IFNULL(round9,0)+
                                IFNULL(round10,0)+IFNULL(round11,0)+IFNULL(round12,0)+
                                IFNULL(round13,0)+IFNULL(round14,0)+IFNULL(round15,0)+
                                IFNULL(round16,0)+IFNULL( round17,0)+IFNULL(round18,0)+ 
                                IFNULL(round19,0)+IFNULL(round20,0)+IFNULL(round21,0)+
                                IFNULL(round22,0)+IFNULL(round23,0)+IFNULL(round24,0)+
                                IFNULL(round25,0)+IFNULL(round26,0)+IFNULL(round27,0)+
                                IFNULL(round28,0)+IFNULL(round29,0)+IFNULL(round30,0)+
                                IFNULL(round31,0)+IFNULL(round32,0)+IFNULL(round33,0)+
                                IFNULL(round34,0)+IFNULL(round35,0)+IFNULL(round36,0)+
                                IFNULL(round37,0)+IFNULL(round38,0)+IFNULL(round39,0)+
                                IFNULL(round40,0)+IFNULL(round41,0)+IFNULL(round42,0)+
                                IFNULL(round43,0)+IFNULL(round44,0)+IFNULL(round45,0)+
                                IFNULL(round46,0)+IFNULL(round47,0)+IFNULL(round48,0)+
                                IFNULL(round49,0)+IFNULL(round50,0)+IFNULL(round51,0)+
                                IFNULL(round52,0)+IFNULL(round53,0)+IFNULL(round54,0)+
                                IFNULL(round55,0)+IFNULL(round56,0)+IFNULL(round57,0)+
                                IFNULL(round58,0)+IFNULL(round59,0)+IFNULL(round60,0)+
                                IFNULL(round61,0)+IFNULL(round62,0)+
                                IFNULL(round63,0)+IFNULL(round64,0)+
                                IFNULL(round65,0)+IFNULL(round66,0)+IFNULL(round67,0)+
                                IFNULL(round68,0)+IFNULL(round69,0)+IFNULL(round70,0)+
                                IFNULL(round71,0)+IFNULL(round72,0)+IFNULL(round73,0)+
                                IFNULL(round74,0)+IFNULL(round75,0)+IFNULL(round76,0)+
                                IFNULL(round77,0)+IFNULL(round78,0)+IFNULL(round79,0)+IFNULL(round80,0)+
                                IFNULL(round81,0)+IFNULL(round82,0)+IFNULL(round83,0)+IFNULL(round84,0)+
                                IFNULL(round85,0)+IFNULL(round86,0)+IFNULL(round87,0)+IFNULL(round88,0)+
                                IFNULL(round89,0)+IFNULL(round90,0)+IFNULL(round91,0)+IFNULL(round92,0)+
                                IFNULL(round93,0)+IFNULL(round94,0)+IFNULL(round95,0)+IFNULL(round96,0)+
                                IFNULL(round97,0)+IFNULL(round98,0)+IFNULL(round99,0)+IFNULL(round100,0)+
                                IFNULL(round101,0)+IFNULL(round102,0)+IFNULL(round103,0)+IFNULL(round104,0)+
                                IFNULL(round105,0)+IFNULL(round106,0)+IFNULL(round107,0)+IFNULL(round108,0)+
                                IFNULL(round109,0)+IFNULL(round110,0)+IFNULL(round111,0)+IFNULL(round112,0)+
                                IFNULL(round113,0)+IFNULL(round114,0)+IFNULL(round115,0)+IFNULL(round116,0)+
                                IFNULL(round117,0)+IFNULL(round118,0)+IFNULL(round119,0)+IFNULL(round120,0)+
                                IFNULL(round121,0)+IFNULL(round122,0)+IFNULL(round123,0)+IFNULL(round124,0)+
                                IFNULL(round125,0)+IFNULL(round126,0)+IFNULL(round127,0)+IFNULL(round128,0)+
                                IFNULL(round129,0)+IFNULL(round130,0)
                                ) AS grant_total, postalballot_vote,".$round;
								
								

                                $sql = DB::table($table);
                                $sql->selectRaw($sql_raw);

                                if(!empty($data['id'])){
                                  $sql->where("id", $data['id']);
                                }

                                if(!empty($data['nom_id'])){
                                  $sql->where("nom_id", $data['nom_id']);
                                }

                                if(!empty($data['ac_no'])){
                                  $sql->where("ac_no", $data['ac_no']);
                                }
                                    $query = $sql->first();
                     
                                return $query;

              }  
         public static function winn_lead($data = array()){ 
            $sql_raw = "leading_id,st_code,ac_no,nomination_id,candidate_id,trail_nomination_id,
                        trail_candidate_id,lead_total_vote,trail_total_vote,margin,status,
                        lead_cand_name,lead_cand_hname,lead_cand_party,lead_cand_hparty,
                        trail_cand_name,trail_cand_hname,trail_cand_party,trail_cand_hparty";

                $sql = DB::table('winning_leading_candidate');
                $sql->selectRaw($sql_raw);
                if(!empty($data['st_code'])){
                  $sql->where("st_code", $data['st_code']);
                }
                if(!empty($data['ac_no'])){
                  $sql->where("ac_no", $data['ac_no']);
                }
                if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                }
                    $query = $sql->first();
     
                return $query;

            }  
        
         public static function master_records($table, $data = array()){ 
            
                $sql = DB::table($table);
                $sql->select('*');
                 
                if(!empty($data['ac_no'])){
                  $sql->where("ac_no", $data['ac_no']);
                  }
                if(!empty($data['election_id'])){
                  $sql->where("election_id", $data['election_id']);
                  }
                  
                 $sql->orderByRaw("new_srno ASC");
                 $sql->orderByRaw("id ASC");
                $query = $sql->get();
                return $query;

            } 
      public static function evm_votes($table,$id,$nom_id,$data = array()){ 
                     $sql_raw = "id, SUM(IFNULL(round1,0)+IFNULL(round2,0)+IFNULL(round3,0)+
                                IFNULL(round4,0)+IFNULL(round5,0)+IFNULL(round6,0)+
                                IFNULL(round7,0)+IFNULL(round8,0)+IFNULL(round9,0)+
                                IFNULL(round10,0)+IFNULL(round11,0)+IFNULL(round12,0)+
                                IFNULL(round13,0)+IFNULL(round14,0)+IFNULL(round15,0)+
                                IFNULL(round16,0)+IFNULL( round17,0)+IFNULL(round18,0)+ 
                                IFNULL(round19,0)+IFNULL(round20,0)+IFNULL(round21,0)+
                                IFNULL(round22,0)+IFNULL(round23,0)+IFNULL(round24,0)+
                                IFNULL(round25,0)+IFNULL(round26,0)+IFNULL(round27,0)+
                                IFNULL(round28,0)+IFNULL(round29,0)+IFNULL(round30,0)+
                                IFNULL(round31,0)+IFNULL(round32,0)+IFNULL(round33,0)+
                                IFNULL(round34,0)+IFNULL(round35,0)+IFNULL(round36,0)+
                                IFNULL(round37,0)+IFNULL(round38,0)+IFNULL(round39,0)+
                                IFNULL(round40,0)+IFNULL(round41,0)+IFNULL(round42,0)+
                                IFNULL(round43,0)+IFNULL(round44,0)+IFNULL(round45,0)+
                                IFNULL(round46,0)+IFNULL(round47,0)+IFNULL(round48,0)+
                                IFNULL(round49,0)+IFNULL(round50,0)+IFNULL(round51,0)+
                                IFNULL(round52,0)+IFNULL(round53,0)+IFNULL(round54,0)+
                                IFNULL(round55,0)+IFNULL(round56,0)+IFNULL(round57,0)+
                                IFNULL(round58,0)+IFNULL(round59,0)+IFNULL(round60,0)+
                                IFNULL(round61,0)+IFNULL(round62,0)+
                                IFNULL(round63,0)+IFNULL(round64,0)+
                                IFNULL(round65,0)+IFNULL(round66,0)+IFNULL(round67,0)+
                                IFNULL(round68,0)+IFNULL(round69,0)+IFNULL(round70,0)+
                                IFNULL(round71,0)+IFNULL(round72,0)+IFNULL(round73,0)+
                                IFNULL(round74,0)+IFNULL(round75,0)+IFNULL(round76,0)+
                                IFNULL(round77,0)+IFNULL(round78,0)+IFNULL(round79,0)+IFNULL(round80,0)+
                                IFNULL(round81,0)+IFNULL(round82,0)+IFNULL(round83,0)+IFNULL(round84,0)+
                                IFNULL(round85,0)+IFNULL(round86,0)+IFNULL(round87,0)+IFNULL(round88,0)+
                                IFNULL(round89,0)+IFNULL(round90,0)+IFNULL(round91,0)+IFNULL(round92,0)+
                                IFNULL(round93,0)+IFNULL(round94,0)+IFNULL(round95,0)+IFNULL(round96,0)+
                                IFNULL(round97,0)+IFNULL(round98,0)+IFNULL(round99,0)+IFNULL(round100,0)+
                                IFNULL(round101,0)+IFNULL(round102,0)+IFNULL(round103,0)+IFNULL(round104,0)+
                                IFNULL(round105,0)+IFNULL(round106,0)+IFNULL(round107,0)+IFNULL(round108,0)+
                                IFNULL(round109,0)+IFNULL(round110,0)+IFNULL(round111,0)+IFNULL(round112,0)+
                                IFNULL(round113,0)+IFNULL(round114,0)+IFNULL(round115,0)+IFNULL(round116,0)+
                                IFNULL(round117,0)+IFNULL(round118,0)+IFNULL(round119,0)+IFNULL(round120,0)+
                                IFNULL(round121,0)+IFNULL(round122,0)+IFNULL(round123,0)+IFNULL(round124,0)+
                                IFNULL(round125,0)+IFNULL(round126,0)+IFNULL(round127,0)+IFNULL(round128,0)+
                                IFNULL(round129,0)+IFNULL(round130,0)) AS grant_total";

                      $sql = DB::table($table);
                      $sql->selectRaw($sql_raw);
                      $sql->where("id", $id);
                      $sql->where("nom_id", $nom_id);
                      $query = $sql->first();
                      return $query;
              } 


      //add by waseem
      public function check_finalized_ro($data = array()){

          $sql  =  DB::table('counting_finalized_ac')
          ->where('st_code',$data['state'])
          ->where('ac_no',$data['ac_no'])
          ->where('election_id',$data['election_id'])
          ->where(function($sql){
            $sql->orWhere('finalized_ac','0')->orWhere('finalize_by_ro','0');
          });
          $object = $sql->count();
          
          if($object > 0){
            return true;
          }else{
            return false;
          }

      }

    public function get_previous_total($data = array()){
         $sub_query  = "";
         $sub_sql  = [];
         $previous_round = $data['round'] - 1;
         if($previous_round != 0){
           for($i = $previous_round; $i > 0; $i--) {
             $sub_sql[] = "IFNULL(round".$i.",0)";
           }

           $round_sql = implode('+',$sub_sql);
           if($round_sql){
             $sub_query .= $round_sql." AS previous_total";
           }
         }else{
           $sub_query = "0 AS previous_total";
         }

         $sub_query .= ", table1.nom_id, table1.ac_no, table1.candidate_name, table1.party_name, table1.party_id";
         $object = DB::table("counting_master_".strtolower($data['st_code'])." as table1")->selectRaw($sub_query)->where('table1.ac_no',$data['ac_no'])->groupBy('table1.ac_no')->groupBy('table1.nom_id')->orderBy('table1.id','ASC')->get();
         return $object;
       } 
      
      public static function getalltendered_vote($data =array())
        {
          $sql_row ="SUM(tendered_vote) AS tendered_vote";
          $sql = DB::table("counting_ps_tendered")->selectRaw($sql_row);
          
         if(!empty($data['st_code'])){
                  $sql->where('st_code',$data['st_code']);
                    }
          if(!empty($data['ac_no'])){
                  $sql->where('ac_no',$data['ac_no']);
                  }
          if(!empty($data['election_id'])){
                  $sql->where('election_id',$data['election_id']);
                  }

           $query = $sql->first();   
          
           return  $query->tendered_vote;
        }  
    }
