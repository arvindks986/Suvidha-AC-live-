<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    protected $table = 'm_state';

    public function Districts()
    {
        return $this->hasMany('App\models\Districts');
    }

    public function AC()
    {
        return $this->hasMany('App\models\AC');
    }
}
