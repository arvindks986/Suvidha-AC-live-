<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;
class ElectiondetailsMaster extends Model
{
 	protected $table ="m_election_details";
	protected $primaryKey ='CCODE'; 
	protected $fillable = [
     	'ScheduleID',
		'CONST_NO',
        'CONST_TYPE',
     	'DELIM_TYPE',
     	'ELECTION_TYPE',
        'ST_CODE',
        'INSERTION_DATE',
        'StatePHASE_NO',
        'PHASE_NO',
        'ELECTION_ID',
        'YEAR'
     ];
	 
    protected $guarded = ['CCODE'];
    
     public function currentelection()
        {
        return $this->belongsTo('App\model\Electioncurrentelection','ST_CODE','ST_CODE');
        }  
    
}
