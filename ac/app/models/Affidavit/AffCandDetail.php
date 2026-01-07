<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffCandDetail extends Model
{
    protected $table = 'aff_cand_details';
    protected $primaryKey ='id';
	protected $fillable = ['affidavit_id','candidate_id','nomination_id','nomination_no','cand_name','name_on_epic','relation_type_of','son_daughter_wife_of','relation_name','dob','age','postal_address','partytype','partyabbre','election_id','st_code','dist_no','ac_no','month','year','cand_sl_no','m_cand_ac_row_id','state_enrolled','dist_no_enrolled','constituency_enrolled','serial_no_enrolled','part_no_enrolled','phoneno_1','std_code','phoneno_2','emailid','cimage','finalized','finalized_on','modified_on'];
}
