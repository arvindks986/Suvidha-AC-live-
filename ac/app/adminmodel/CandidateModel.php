<?php
	namespace App\adminmodel;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use App\adminmodel\CandidateNomination;
class CandidateModel extends Model
	{
     protected $table ="candidate_personal_detail";
	 protected $primaryKey ='candidate_id'; 
	 
	public $timestamps = false;
	
	protected $fillable = ['cand_name','candidate_father_name','cand_email','cand_mobile','candidate_residence_address','candidate_residence_pcno','candidate_residence_districtno','candidate_residence_pincode','candidate_residence_acno','candidate_residance_part_no','candidate_residence_stcode','cand_age','cand_category','cand_panno','candidate_temporary_address','created_by'];
	protected $guarded = ['candidate_id'];
	
	public function nomination()
    {
	   return $this->hasMany('App\Models\CandidateNomination', 'candidate_id', 'candidate_id');
    }

}