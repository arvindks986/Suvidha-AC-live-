<?php
	namespace App\adminmodel;
	use Illuminate\Database\Eloquent\Model;
	use DB;
class CandidateNomination extends Model
{
    protected $table ="candidate_nomination_detail";
	protected $primaryKey ='nom_id'; 
	protected $fillable = [
     	'candidate_id',
		'party_id',
        'symbol_id',
     	'election_id',
     	'form_upload',
     	'part_no',
     	'date_of submit',
     	'ac_no',
     	'pc_no',
     	'st_code',
     	'district_no',
     	'nomination_papersrno',
     	'rosubmit_time',
     	'rosubmit_date',
     	'nomination_submittedby',
     	'rejection_message',
     	'scrutiny_time',
     	'scrutiny_date',
     	'place',
     	'fdate'
     ];
	protected $guarded = ['nom_id'];

    public function partys()
    	{
        return $this->belongsTo('App\model\PartyMaster','party_id','CCODE');
    	}
    public function symbols()
    	{
        return $this->belongsTo('App\model\SymbolMaster','symbol_id','SYMBOL_NO');
    	}
    public function candidates()  
    	{
        return $this->belongsTo('App\model\CandidateModel','candidate_id','candidate_id');
    	}
    public function applycandidates()
        {
        return $this->belongsTo('App\model\CandidateModel','candidate_id','candidate_id');
        
        }
    public function affidavit()
        {
        return $this->belongsTo('App\model\Candidateaffidavit','nom_id','nom_id');
        }
}

