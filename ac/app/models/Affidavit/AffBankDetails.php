<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffBankDetails extends Model
{
    protected $table = 'aff_bank_details';
    protected $primaryKey ='id';
	protected $fillable = ['candidate_id','nomination_id','nomination_no','name','occupation','relation_type_code','relation_code','pan','financial_year'];
}
