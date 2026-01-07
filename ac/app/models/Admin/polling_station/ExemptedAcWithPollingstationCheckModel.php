<?php

namespace App\models\Admin\polling_station;

use App\models\AC;
use Illuminate\Database\Eloquent\Model;

class ExemptedAcWithPollingstationCheckModel extends Model
{
  protected $table = 'exempted_ac_with_pollingstation_check';

  protected $fillable = [
    'st_code',
    'ac_no',
    'election_id'
  ];

  public function ac()
  {
    return $this->belongsTo(AC::class, 'ac_no', 'AC_NO');
  }
}
