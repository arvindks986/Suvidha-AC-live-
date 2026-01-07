<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffImprisonmentCriminalCase extends Model
{
    protected $table = 'aff_imprisonment_criminal_cases';
    protected $primaryKey ='id';
    protected $fillable = ['affidavit_id','candidate_id','not_applicable','acts','sections','offence_description','convicting_court','case_no','order_date','punish','appeal_filed','appeal','modified_on'];
}
