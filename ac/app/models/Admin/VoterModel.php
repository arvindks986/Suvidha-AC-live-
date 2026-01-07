<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class VoterModel extends Model
{
    protected $table = 'electors_cdac_other_information';

    protected $fillable = ['id', 'year', 'st_code', 'ac_no', 'general_male_voters', 'general_female_voters', 'general_other_voters', 'nri_male_voters', 'nri_female_voters', 'nri_other_voters', 'test_votes_49_ma', 'votes_not_retreived_from_evm','votes_counted_from_evm','votes_counted_from_vvpat', 'rejected_votes_due_2_other_reason', 'service_postal_votes_under_section_8', 'service_postal_votes_gov', 'postal_votes_rejected', 'proxy_votes', 'tendered_votes', 'total_polling_station_s_i_t_c', 'date_of_repoll', 'no_poll_station_where_repoll', 'is_by_or_countermanded_election', 'reasons_for_by_or_countermanded_election', 'submitted_by', 'created_at', 'updated_at'];

    public $timestamps = false;

    public static function get_voter_by_pc($data = array()){
        $result = [
            'general_male_voters' => 0, 
            'general_female_voters' => 0, 
            'general_other_voters' => 0,
            'nri_male_voters'  => 0,  
            'nri_female_voters'  => 0,  
            'nri_other_voters'  => 0,  
            'test_votes_49_ma'  => 0,  
            'votes_not_retreived_from_evm'  => 0,  
            'votes_counted_from_evm'  => 0,  
            'votes_counted_from_vvpat'  => 0,  
            'rejected_votes_due_2_other_reason'  => 0,  
            'service_postal_votes_under_section_8'  => 0,  
            'service_postal_votes_gov'  => 0,  
            'postal_votes_rejected'  => 0,  
            'proxy_votes'  => 0,  
            'tendered_votes'  => 0,  
            'total_polling_station_s_i_t_c'  => 0,  
            'date_of_repoll'  => '',  
            'no_poll_station_where_repoll'  => '',  
            'is_by_or_countermanded_election'  => 0,  
            'reasons_for_by_or_countermanded_election'  => '',
        ];
        $object = VoterModel::where('ac_no', $data['ac_no'])->where('st_code', $data['st_code'])
		//->where('year', $data['year'])
		->first();
        if($object){
            $result = $object->toArray();
        }
        return $result;
    }

    public static function update_index_card_pc_data($data = array(), $filter = array()){
        $object = VoterModel::firstOrNew($filter);
        if($object->save()){
            $object->update($data);
        }
    }

    public static function get_finalize_pc($filter = array()){
        $total = VoterModel::where('ac_no', $filter['ac_no'])->where('st_code', $filter['st_code'])
		//->where('year', $filter['year'])
		->where('finalize',1)->count();
        return $total;
    }

    public static function check_indexcard_pc_entry($filter = array()){
        $object = VoterModel::where('ac_no', $filter['ac_no'])->where('st_code', $filter['st_code'])
		//->where('year', $filter['year'])
		->first();
        if(!$object){
            return false;
        }
        return true;
    }

    public static function update_finalize_pc($filter = array()){
        $object = VoterModel::where('ac_no', $filter['ac_no'])->where('st_code', $filter['st_code'])
		//->where('year', $filter['year'])
		->first();
		
		//dd($object);
		
        $object->finalize 				= 1;
        $object->ro_name 				= $filter['ro_name'];
        $object->ro_certificate 		= $filter['ro_certificate'];
		$object->date_of_finalize_by_ro = date('Y-m-d');
        $object->finalize_by 			= \Auth::user()->officername;
        if(!empty($filter['finalize_by_ro'])){
            $object->finalize_by_ro = 1;
        }
        if(!empty($filter['finalize_by_ceo'])){
            $object->finalize_by_ceo = 1;
        }
        if(!empty($filter['finalize_by_eci'])){
            $object->finalize_by_eci = 1;
        }
        return $object->save();
    }

    public static function update_finalize_ceo($data = array()){
        $objects = VoterModel::where('id', $data['id'])->where('st_code', $data['st_code'])
		//->where('year', $data['year'])
		->where('finalize','1')->where('finalize_by_ro','1')->first();
        if(!$objects){
            return false;
        }
        $objects->finalize_by_ceo 	= 1;
        $objects->ceo_name 			= $data['ceo_name'];
        $objects->ceo_certificate 	= $data['ceo_certificate'];
		$objects->date_of_finalize_by_ceo = date('Y-m-d');
        return $objects->save();
    }

    public static function update_definalize_ceo($data = array()){
        $objects = VoterModel::where('id', $data['id'])->where('st_code', $data['st_code'])
		//->where('year', $data['year'])
		->where('finalize','1')->where('finalize_by_ro','1')->first();
        if(!$objects){
            return false;
        }
        $objects->finalize_by_ceo = 0;
        $objects->finalize_by_ro = 0;
        $objects->finalize = 0;
        return $objects->save();
    }
	
	//result date update
    public static function get_result_declared_date($st_code, $ac_no){
        $object = \DB::table("winning_leading_candidate")->where("st_code", $st_code)->where("ac_no", $ac_no)->first();
        if(!$object){
            return "2019-05-23";
        }
        return $object->result_declared_date;
    }

    public static function update_result_date($date, $filter){
        \DB::table("winning_leading_candidate")->where($filter)->update(['result_declared_date' => $date]);
    }
    

}