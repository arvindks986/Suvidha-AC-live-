<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;

class MELECMaster extends Model
{
    protected $table ="m_elec";
	protected $primaryKey ='ELEC_ID'; 
	protected $fillable = [
     	'ELEC_MONTH',
		'ELEC_YEAR',
        'ELECTION_ID',
      ]; 
	 
    protected $guarded = ['ELEC_ID'];
}
