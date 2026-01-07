<?php

namespace App\models\Admin\E_Plan;

use Illuminate\Database\Eloquent\Model;

class AddAccountEpicModel extends Model
{
    
    protected $table = 'eplan_account_info_epic';

    protected $fillable = ['id', 'st_code', 'dist_no','bank_name','amount_for_duplicate_epic','account_payment_for', 'account_name','account_mobile','account_email','account_number','account_type','account_ifsc','account_benificeary','is_finalised','is_verified','created_at','updated_at'];
    

}