<?php
	namespace App\adminmodel;
	use Illuminate\Database\Eloquent\Model;
	use DB;
class Candidateaffidavit extends Model
{
    protected $table ="candidate_affidavit_detail";
	protected $primaryKey ='idcandidate_affidavit_detail'; 
	protected $fillable = [
     	'candidate_id',
		'nom_id',
        'affidavit_name',
     	'affidavit_path',
     	'created_at',
     	'created_by' 
     ];
	protected $guarded = ['idcandidate_affidavit_detail'];
 
    public function nomination()
    	{
        return $this->belongsTo('App\model\CandidateNomination','nom_id','nom_id');
    	}  
}