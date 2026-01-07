<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffEducation extends Model
{
    protected $table = 'aff_cand_qualification';
    protected $primaryKey ='id';
	protected $fillable = ['candidate_id'];
}
