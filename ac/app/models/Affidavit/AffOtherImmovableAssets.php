<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffOtherImmovableAssets extends Model
{
    protected $table = 'aff_other_immovable_assets';
    protected $primaryKey ='id';
	protected $fillable = ['candidate_id'];
}
