<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PwdPickAndDrop extends Model
{
    use SoftDeletes;
    protected $table = 'pwd_pick_and_drop';

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
        'address',
        'ps_no',
        'ps_name',
        'remarks'
    ];

    public function ac()
    {
        return $this->belongsTo(AC::class, 'ac_no', 'AC_NO');
    }
}
