<?php

namespace App\models;

use App\models\States;
use Illuminate\Database\Eloquent\Model;

class AC extends Model
{
    protected $table = 'm_ac';

    public function state()
    {
        return $this->belongsTo(States::class, 'ST_CODE', 'ST_CODE');
    }

    public function district()
    {
        return $this->belongsTo(Districts::class, 'DIST_NO_HDQTR', 'DIST_NO');
    }
}
