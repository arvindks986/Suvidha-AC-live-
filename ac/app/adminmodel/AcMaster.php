<?php

namespace App\adminmodel;

use Illuminate\Database\Eloquent\Model;

class AcMaster extends Model
{
    protected $table ="m_ac";
	protected $primaryKey ='CCODE'; 
	protected $fillable = [
     	'ST_CODE',
		'AC_NO',
        'AC_NAME',
     	'AC_TYPE',
     	'PC_NO',
     	'AC_NAME_HI',
     ]; 
	 
    protected $guarded = ['CCODE'];
   public function state()
        {
        return $this->hasmany('App\model\StateMaster','ST_CODE','ST_CODE');
        }
}
