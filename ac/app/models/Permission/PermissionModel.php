<?php

namespace App\models\Permission;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class PermissionModel extends Model
{
    protected $table = 'permission_request';
    protected $primaryKey ='id'; 
    public $timestamps = false;
    protected $fillable =['user_id',
    						'st_code',
    						'dist_no',
    						'ac_no',
                           	'pc_no',
                            'party_id',
    						'permission_type_id',
                            'required_files',
    						'location_id',
							'fileserver_dir',
    						'Other_location',
    						'latitude',
    						'longitude',
    						'date_time_start',
    						'date_time_end',
    						'assigned_police_st_id',
    						'draft_status',
    						'approved_status',
                            'permission_mode',
                            'added_at',
    						'created_at',
    						'updated_at',
    						'created_by',
    						'updated_by',
                            'action_date','election_id','did'
    					];
   
}
