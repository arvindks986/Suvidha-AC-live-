<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PwdVolunteer extends Model
{
    use SoftDeletes;
    protected $table = 'pwd_volunteer';

    protected $fillable = [
        'referenceid',
        'ac_no',
        'epic_no',
        'name',
        'mobile',
        'address',
        'st_code',
        'remarks'
    ];

    public function ac()
    {
        return $this->belongsTo(AC::class, 'ac_no', 'AC_NO');
    }
}
