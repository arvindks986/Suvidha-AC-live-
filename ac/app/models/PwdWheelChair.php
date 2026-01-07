<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PwdWheelChair extends Model
{
    use SoftDeletes;
    protected $table = 'pwd_wheel_chair';

    protected $fillable = [
        'referenceid',
        'electiontype',
        'st_code',
        'ac_no',
        'pc_no',
        'epic_no',
        'name',
        'age',
        'mobile',
        'ps_no',
        'ps_name',
        'remarks'
    ];

    public function ac()
    {
        return $this->belongsTo(AC::class, 'ac_no', 'AC_NO');
    }
}
