<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffDepositType extends Model
{
    protected $table = 'aff_m_deposit_type';
    protected $primaryKey ='id';
	protected $fillable = ['candidate_id','nomination_id','nomination_no','name','occupation','relation_type_code','relation_code','pan','financial_year'];
}
