<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffPanDetail extends Model
{
    protected $table = 'aff_pan_details';
    protected $primaryKey ='id';
	protected $fillable = ['affidavit_id','candidate_id','nomination_id','nomination_no','name','occupation','relation_type_code','relation_code','pan','financial_year','modified_on'];
}
