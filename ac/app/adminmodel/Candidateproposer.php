<?php
	namespace App\adminmodel;
	use Illuminate\Database\Eloquent\Model;
	use DB;
class Candidateproposer extends Model
{
    protected $table ="candidate_proposer_details";
	protected $primaryKey ='proposer_id'; 
	protected $fillable = [
     	'candidate_id',
        'nom_id',
		'proposer_name',
        'proposer_slno',
     	'proposer_partno',
     	'proposer_stcode',
     	'proposer_acno',
        'proposer_pcno',
        'proposer_date'  
     ];
	protected $guarded = ['candidate_proposer_details'];
  
}