<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PC extends Model
{
	protected $table = 'm_pc';
	
	public function state()
    {
        return $this->belongsTo('App\models\States');
    }
}
