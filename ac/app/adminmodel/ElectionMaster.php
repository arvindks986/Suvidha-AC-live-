<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;

class ElectionMaster extends Model
{
    protected $table ="election_master";
	protected $primaryKey ='election_id'; 
	protected $fillable = [
     	'election_name',
		'election_type',
        'election_sort_name', 
     ]; 
	 
    protected $guarded = ['election_id'];
    
}
