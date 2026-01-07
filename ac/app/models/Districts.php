<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Districts extends Model
{
    protected $table = 'm_district';
	
	public function state()
    {
        return $this->belongsTo('App\models\States');
    }
}
