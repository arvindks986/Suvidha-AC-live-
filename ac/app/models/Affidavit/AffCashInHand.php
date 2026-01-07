<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffCashInHand extends Model
{
    protected $table = 'aff_cash_in_hand';
    protected $primaryKey ='id';
	protected $fillable = ['affidavit_id','candidate_id','nomination_id','nomination_no','relation_type_code','cash_in_hand','added_create_at','added_update_at','created_by','updated_by','modified_on'];
}
