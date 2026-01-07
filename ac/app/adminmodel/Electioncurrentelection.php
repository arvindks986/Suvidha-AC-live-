<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;
class Electioncurrentelection extends Model
{
 	protected $table ="m_cur_elec";
	protected $primaryKey ='ID'; 
	protected $fillable = [
     	'ST_CODE',
		'MONTH',
        'YEAR',
     	'DelmType',
     	'ElecType',
        'ST_CODE',
        'ConstType',
        'DaysForRO',
        'PHASE_NO',
        'CURRENTELECTION',
        'ELECTION_ID'
     ];
	 
    protected $guarded = ['ID'];

    public function electiondetails()
        {
        return $this->hasmany('App\model\ElectiondetailsMaster','ST_CODE','ST_CODE');
        }  
      
}
