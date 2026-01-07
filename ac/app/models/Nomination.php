<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Nomination extends Model
{
    protected $table ="candidate_personal_detail";
	protected $primaryKey ='candidate_id'; 
	 
	public $timestamps = false;
	
	protected $fillable = ['party_id','candidate_name','candidate_age','candidate_gender','candidate_category','candidate_panno','candidate_residence_address','candidate_temporary_address','created_by'];
	protected $guarded = ['candidate_id'];
	
	public function nomination()
    {

        return $this->hasMany('App\Models\nomination', 'candidate_id', 'candidate_id');
    }
	function getUserRole(){
		$getUserRole = DB::table('user_role')->get();
    	return ($getUserRole);
	}
	function getUserRolebyroleid(){
		$getUserRolebyroleid = DB::table('user_role')->where('role_level',2)->get();
    	return ($getUserRolebyroleid);
	} 
	function getCandidateByUserid($username)
	{ 
		if(is_numeric($username)){
		$getCandidateByUserid = DB::table('candidate_personal_detail')->where('cand_mobile', $username)->first();
		}else{
		$getCandidateByUserid = DB::table('candidate_personal_detail')->where('cand_email', $username)->first();
		}
		//dd($getCandidateByUserid);
		return $getCandidateByUserid;
	}
	function getElectionTypeDetail($consType,$electionType)
	{ 	
		$getElectionTypeDetail = DB::table('election_master')->where('election_type', $electionType)->where('election_sort_name', $consType)->first();
		//dd($getElectionTypeDetail);
		return $getElectionTypeDetail;
	}
}
