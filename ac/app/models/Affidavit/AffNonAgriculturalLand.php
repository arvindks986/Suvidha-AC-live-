<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffNonAgriculturalLand extends Model
{
    protected $table = 'aff_non_agricultural_land_details';
    protected $primaryKey ='id';
	protected $fillable = ['candidate_id'];
}
