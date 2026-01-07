<?php
	namespace App\adminmodel;
	use Illuminate\Database\Eloquent\Model;
	use DB;
class Candidatecriminal extends Model
{
    protected $table ="candidate_criminal_case";
	protected $primaryKey ='rowid'; 
	protected $fillable = [
     	'case_no',
		'police_station',
        'state_code',
     	'district_no',
     	'convicted_des',
     	'date_of_conviction',
        'court_name',
        'punishment_imposed',
        'Date_of_release'
     ];
	protected $guarded = ['rowid'];
 
}