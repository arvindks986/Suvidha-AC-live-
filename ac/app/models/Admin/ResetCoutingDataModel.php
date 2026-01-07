<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Auth, Session, Cookie, DB;

class ResetCoutingDataModel extends Model
{
    protected $table = 'officer_login';

    public static function get_officer($id){
        $object =  ResetCoutingDataModel::where('st_code', Auth::user()->st_code)->find($id);
        if(!$object){
            return false;
        }
        return $object->toArray();
    }
	
    public static function get_users($data = array()){
        $sql = ResetCoutingDataModel::leftJoin('m_state','m_state.ST_CODE','=','officer_login.st_code')->select('officer_login.*','m_state.ST_NAME as state_name');
        if(!empty($data['token'])){
            $sql->where('officer_login.otp_verify_by_string', $data['token']);
        }
        if(!empty($data['mobile'])){
            $sql->where('officer_login.Phone_no', $data['mobile']);
        }
        if(!empty($data['otp'])){
            $sql->where('officer_login.mobile_otp', $data['otp']);
        }
        if(!empty($data['st_code'])){
            $sql->where('officer_login.st_code', $data['st_code']);
        }
        if(!empty($data['role_id']) && count($data['role_id'])>0){
            $sql->whereIn('officer_login.role_id', $data['role_id']);
        }
        $object = $sql->orderByRaw('officer_login.st_code, officer_login.pc_no ASC')->get();
        if(!$object){
            return [];
        }
        return $object->toArray();
    }

    public static function reset_counting_data_by_ro($data = array()){

        DB::select("UPDATE counting_finalized_ac SET finalized_ac = '0', finalize_by_ceo = '0', finalize_by_ro = '0' where st_code = '".$data['st_code']."' and ac_no = '".$data['ac_no']."'");
        DB::select("UPDATE winning_leading_candidate SET candidate_id = '0', nomination_id = '0', lead_cand_name = '', lead_cand_hname = '', lead_cand_partyid = '0', lead_cand_party = '',lead_cand_hparty = '', lead_party_type = '',lead_party_abbre = '',lead_hpartyabbre = '',trail_candidate_id = '0',trail_nomination_id = '0', trail_cand_name = '',trail_cand_hname = '',trail_cand_partyid = '0',trail_cand_party = '',trail_cand_hparty = '',trail_party_type = '', trail_party_abbre = '',trail_hpartyabbre = '',lead_total_vote = '0',trail_total_vote = '0',margin = '0', status='0' where st_code='".$data['st_code']."' and ac_no = '".$data['ac_no']."'");
        DB::select("DELETE from round_master where st_code='".$data['st_code']."' and ac_no = '".$data['ac_no']."'");
        DB::select("UPDATE counting_master_".$data['st_code']." set round1 = '0', round2 = '0', round3 = '0', round4 = '0', round5 = '0', round6 = '0', round7 = '0', round8 = '0', round9 = '0', round10 = '0', round11 = '0', round12 = '0', round13 = '0', round14 = '0', round15 = '0', round16 = '0', round17 = '0', round18 = '0', round19 = '0', round20 = '0', round21 = '0', round22 = '0', round23 = '0', round24 = '0', round25 = '0', round26 = '0', round27 = '0', round28 = '0', round29 = '0', round30 = '0', round31 = '0', round32 = '0', round33 = '0', round34 = '0', round35 = '0', round36 = '0', round37 = '0', round38 = '0', round39 = '0', round40 = '0', round41 = '0', round42 = '0', round43 = '0', round44 = '0', round45 = '0', round46 = '0', round47 = '0', round48 = '0', round49 = '0', round50 = '0', round51 = '0',postalballot_vote = '0', total_vote = '0', complete_round = '0',finalized_round = '0' where ac_no = '".$data['ac_no']."'");

    }

    public static function reset_counting_state($data = array()){

        //change
        DB::select("UPDATE counting_finalized_ac SET finalized_ac = '0', finalize_by_ceo = '0', finalize_by_ro = '0' where st_code = '".$data['st_code']."'");
        DB::select("UPDATE winning_leading_candidate SET candidate_id = '0', nomination_id = '0', lead_cand_name = '', lead_cand_hname = '', lead_cand_partyid = '0', lead_cand_party = '',lead_cand_hparty = '', lead_party_type = '',lead_party_abbre = '',lead_hpartyabbre = '',trail_candidate_id = '0',trail_nomination_id = '0', trail_cand_name = '',trail_cand_hname = '',trail_cand_partyid = '0',trail_cand_party = '',trail_cand_hparty = '',trail_party_type = '', trail_party_abbre = '',trail_hpartyabbre = '',lead_total_vote = '0',trail_total_vote = '0',margin = '0', status='0' where st_code='".$data['st_code']."'");
        DB::select("DELETE from round_master where st_code='".$data['st_code']."'");
        DB::select("UPDATE counting_master_".$data['st_code']." set round1 = '0', round2 = '0', round3 = '0', round4 = '0', round5 = '0', round6 = '0', round7 = '0', round8 = '0', round9 = '0', round10 = '0', round11 = '0', round12 = '0', round13 = '0', round14 = '0', round15 = '0', round16 = '0', round17 = '0', round18 = '0', round19 = '0', round20 = '0', round21 = '0', round22 = '0', round23 = '0', round24 = '0', round25 = '0', round26 = '0', round27 = '0', round28 = '0', round29 = '0', round30 = '0', round31 = '0', round32 = '0', round33 = '0', round34 = '0', round35 = '0', round36 = '0', round37 = '0', round38 = '0', round39 = '0', round40 = '0', round41 = '0', round42 = '0', round43 = '0', round44 = '0', round45 = '0', round46 = '0', round47 = '0', round48 = '0', round49 = '0', round50 = '0', round51 = '0',postalballot_vote = '0', total_vote = '0', complete_round = '0',finalized_round = '0'");




    }



}
