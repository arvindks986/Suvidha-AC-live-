<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffCommercialBuildings extends Model
{
    protected $table = 'aff_commercial_buildings_details';
    protected $primaryKey ='id';
	protected $fillable = ['candidate_id'];
}
