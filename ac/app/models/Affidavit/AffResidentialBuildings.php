<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffResidentialBuildings extends Model
{
    protected $table = 'aff_residential_buildings_details';
    protected $primaryKey ='id';
	protected $fillable = ['candidate_id'];
}
