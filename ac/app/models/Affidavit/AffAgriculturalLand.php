<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffAgriculturalLand extends Model
{
    protected $table = 'aff_agricultural_land_details';
    protected $primaryKey ='id';
	protected $fillable = ['candidate_id'];
}
