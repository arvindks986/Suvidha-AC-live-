<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;
class StateMaster extends Model
{
 	protected $table ="m_state";
	protected $primaryKey ='CCODE'; 
	protected $fillable = [
     	'ST_CODE',
		'ST_NAME',
        'SHORTNAME',
     	'ST_TYPE',
     	'ST_NAME_HI'
     ];
	 
    public function Districts()
        {
        return $this->hasMany('App\models\Districts');
        }
    
    public function AC()
        {
        return $this->hasMany('App\models\AC');
        }
}
