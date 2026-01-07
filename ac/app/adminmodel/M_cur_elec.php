<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;

class M_cur_elec extends Model
{
    protected $table ="m_cur_elec";
	protected $primaryKey ='ID'; 
	protected $fillable = [
     	'ELEC_MONTH',
		'ELEC_YEAR',
        'ELECTION_ID',
      ]; 
	 
    protected $guarded = ['ID'];
}
