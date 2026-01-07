<?php

namespace App\models\permission;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;
class User_dataModel extends Model
{
    protected $table = 'user_data';
    protected $primaryKey ='id'; 
    public $timestamps = false;
    protected $fillable =['user_login_id',
							'name',
							'fathers_name',
							'email',
							'mobileno',
							'gender',
							'epic_no',
							'part_no',
							'slno',
							'dob',
							'party_id',
							'address',
							'state_id',
							'district_id',
							'ac_id',
							'religion',
							'caste',
							'mark_as_delete',
							'added_at',
							'created_at',
							'added_update_at',
							'updated_at','election_id'
    					];
}




